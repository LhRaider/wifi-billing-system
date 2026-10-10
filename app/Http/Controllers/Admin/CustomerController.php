<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\AuditHelper;
use App\Helpers\NotificationHelper;
use App\Http\Controllers\Controller;
use App\Models\HotspotUser;
use App\Models\Router;
use App\Models\Subscription;
use App\Models\User;
use App\Services\MikrotikService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class CustomerController extends Controller
{
    /**
     * Display only customers belonging to the authenticated ISP Admin.
     */
    public function index(Request $request)
    {
        $admin = $this->currentAdmin();

        $search = trim((string) $request->query('search', ''));

        $customers = User::query()
            ->where('role', 'customer')
            ->where('admin_id', $admin->id)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%');
                });
            })
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.customers.index', compact(
            'customers',
            'search'
        ));
    }

    /**
     * Display one customer belonging to the authenticated ISP Admin.
     */
    public function show(User $customer)
    {
        $admin = $this->currentAdmin();

        $this->ensureCustomerBelongsToAdmin(
            $customer,
            $admin->id
        );

        $totalPayments = $customer->payments()
            ->count();

        $totalSpent = $customer->payments()
            ->where('status', 'completed')
            ->sum('amount');

        $lastPayment = $customer->payments()
            ->latest('id')
            ->first();

        $latestSubscription = $customer->subscriptions()
            ->with('package')
            ->latest('id')
            ->first();

        $latestHotspotUser = $customer->hotspotUsers()
            ->with('router')
            ->latest('id')
            ->first();

        /*
         * Only routers belonging to this ISP Admin can be selected when
         * provisioning a customer's first network access credential.
         */
        $activeRouters = Router::query()
            ->where('admin_id', $admin->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.customers.show', compact(
            'customer',
            'totalPayments',
            'totalSpent',
            'lastPayment',
            'latestSubscription',
            'latestHotspotUser',
            'activeRouters'
        ));
    }

    /**
     * Provision the customer's first reusable MikroTik network credential.
     *
     * The same local credential record is used for both Hotspot and PPPoE.
     * The current valid subscription determines which RouterOS access type
     * is provisioned.
     */
    public function provisionNetworkAccess(
        Request $request,
        User $customer,
        MikrotikService $mikrotik
    ) {
        $admin = $this->currentAdmin();

        $this->ensureCustomerBelongsToAdmin(
            $customer,
            $admin->id
        );

        if ($customer->is_suspended) {
            return back()->with(
                'error',
                'Network access cannot be provisioned while this customer is suspended.'
            );
        }

        /*
         * A customer has exactly one reusable network credential.
         * The database also enforces uniqueness on user_id.
         */
        if (
            HotspotUser::query()
                ->where('user_id', $customer->id)
                ->exists()
        ) {
            return back()->with(
                'error',
                'This customer already has a network access account.'
            );
        }

        $validated = $request->validate([
            'router_id' => [
                'required',
                'integer',
            ],
        ]);

        /*
         * Never trust a submitted router ID by itself. The router must
         * belong to the authenticated ISP Admin and must be active.
         */
        $router = Router::query()
            ->whereKey($validated['router_id'])
            ->where('admin_id', $admin->id)
            ->where('is_active', true)
            ->first();

        if (!$router) {
            return back()
                ->withErrors([
                    'router_id' =>
                        'Select a valid active router belonging to your ISP account.',
                ])
                ->withInput();
        }

        /*
         * Provisioning requires a currently valid active subscription.
         * Historical, future, suspended, and expired subscriptions cannot
         * be used to create live router access.
         */
        $subscription = Subscription::with('package')
            ->where('user_id', $customer->id)
            ->where('status', 'active')
            ->where('start_time', '<=', now())
            ->where('end_time', '>', now())
            ->latest('id')
            ->first();

        if (!$subscription) {
            return back()->with(
                'error',
                'This customer does not have a currently valid active subscription. Network access was not provisioned.'
            );
        }

        $package = $subscription->package;

        if (
            !$package ||
            (int) $package->admin_id !== (int) $admin->id ||
            !$package->is_active
        ) {
            return back()->with(
                'error',
                'The active subscription does not have a valid active package belonging to this ISP Admin.'
            );
        }

        if (!in_array($package->package_type, ['hotspot', 'pppoe'], true)) {
            return back()->with(
                'error',
                'The subscription package has an unsupported network access type.'
            );
        }

        /*
         * user{id} keeps credentials easy for the ISP Admin to identify.
         * We still check existing usernames because username is not unique
         * at the database level.
         */
        $username = $this->generateNetworkUsername($customer);

        /*
         * Generate the initial network password server-side.
         * It is encrypted automatically by the HotspotUser model.
         */
        $password = Str::random(16);

        $credential = null;

        try {
            /*
             * Create the local record first in a non-active/non-synchronized
             * state. It becomes active only after RouterOS confirms success.
             */
            $credential = HotspotUser::create([
                'user_id' => $customer->id,
                'subscription_id' => $subscription->id,
                'router_id' => $router->id,
                'username' => $username,
                'password' => $password,
                'profile' => $package->router_profile,
                'status' => 'inactive',
                'router_synced' => false,
                'last_sync_status' => 'Pending',
                'last_sync_message' =>
                    'Waiting for initial MikroTik provisioning.',
            ]);

            $credential->load([
                'user',
                'router',
                'subscription.package',
            ]);

            if ($package->package_type === 'pppoe') {
                $result = $mikrotik->createPppoeUser(
                    $credential->username,
                    $credential->password,
                    $package,
                    $router
                );
            } else {
                $result = $mikrotik->createHotspotUser(
                    $credential
                );
            }

            if (!($result['success'] ?? false)) {
                $message = $result['message']
                    ?? 'MikroTik provisioning failed.';

                $credential->update([
                    'status' => 'inactive',
                    'router_synced' => false,
                    'last_sync_status' => 'Failed',
                    'last_sync_message' => $message,
                ]);

                Log::warning(
                    'Customer network access provisioning failed.',
                    [
                        'admin_id' => $admin->id,
                        'customer_id' => $customer->id,
                        'subscription_id' => $subscription->id,
                        'router_id' => $router->id,
                        'package_type' => $package->package_type,
                        'credential_id' => $credential->id,
                        'message' => $message,
                    ]
                );

                return back()->with(
                    'warning',
                    'The network account was created locally, but MikroTik provisioning failed. Use Network Access Management to retry synchronization.'
                );
            }

            $credential->update([
                'status' => 'active',
                'router_synced' => true,
                'last_sync_status' => 'Success',
                'last_sync_message' =>
                    $result['message']
                    ?? 'Network access provisioned successfully.',
            ]);

            AuditHelper::log(
                'Provision Network Access',
                'Customers',
                $customer->name.' network access was provisioned on '.$router->name.'.'
            );

            NotificationHelper::create(
                'Network Access Provisioned',
                $customer->name.' network access has been provisioned successfully.',
                'success',
                'admin',
                $admin->id
            );

            return back()->with(
                'success',
                strtoupper($package->package_type).' network access provisioned successfully.'
            );
        } catch (Throwable $e) {
            /*
             * If the local credential was already created, preserve it as a
             * failed record so the generated encrypted password and failure
             * state remain available for a secure retry.
             */
            if ($credential && $credential->exists) {
                try {
                    $credential->update([
                        'status' => 'inactive',
                        'router_synced' => false,
                        'last_sync_status' => 'Failed',
                        'last_sync_message' =>
                            'Unexpected error during MikroTik provisioning.',
                    ]);
                } catch (Throwable $updateException) {
                    Log::error(
                        'Could not update failed network credential state.',
                        [
                            'credential_id' => $credential->id,
                            'error' => $updateException->getMessage(),
                        ]
                    );
                }
            }

            Log::error(
                'Unexpected customer network access provisioning error.',
                [
                    'admin_id' => $admin->id,
                    'customer_id' => $customer->id,
                    'subscription_id' => $subscription->id,
                    'router_id' => $router->id,
                    'error' => $e->getMessage(),
                ]
            );

            return back()->with(
                'error',
                'Network access could not be provisioned because an unexpected error occurred. No active router access was confirmed.'
            );
        }
    }

    /**
     * Suspend a customer and immediately revoke local service entitlement.
     *
     * Any current active subscription becomes suspended rather than expired.
     * Router access is then disabled where possible.
     */
    public function suspend(
        User $customer,
        MikrotikService $mikrotik
    ) {
        $admin = $this->currentAdmin();

        $this->ensureCustomerBelongsToAdmin(
            $customer,
            $admin->id
        );

        if ($customer->is_suspended) {
            return back()->with(
                'success',
                'This customer is already suspended.'
            );
        }

        $activeSubscription = Subscription::with('package')
            ->where('user_id', $customer->id)
            ->where('status', 'active')
            ->first();

        DB::transaction(function () use (
            $customer,
            $activeSubscription
        ) {
            $customer->update([
                'is_suspended' => true,
            ]);

            if ($activeSubscription) {
                $activeSubscription->update([
                    'status' => 'suspended',
                ]);
            }
        });

        $routerSyncFailed = false;

        $hotspotUser = HotspotUser::with([
            'user',
            'router',
            'subscription.package',
        ])
            ->where('user_id', $customer->id)
            ->first();

        if ($hotspotUser) {
            if (
                !$hotspotUser->user ||
                $hotspotUser->user->role !== 'customer' ||
                (int) $hotspotUser->user->admin_id !== (int) $admin->id
            ) {
                $hotspotUser->update([
                    'status' => 'inactive',
                    'router_synced' => false,
                    'last_sync_status' => 'Failed',
                    'last_sync_message' =>
                        'Tenant ownership verification failed during customer suspension.',
                ]);

                $routerSyncFailed = true;

                Log::error(
                    'Customer suspension router credential tenant verification failed.',
                    [
                        'customer_id' => $customer->id,
                        'hotspot_user_id' => $hotspotUser->id,
                        'admin_id' => $admin->id,
                    ]
                );
            } else {
                $package = $activeSubscription?->package
                    ?? $hotspotUser->subscription?->package;

                if (
                    $package &&
                    (int) $package->admin_id === (int) $admin->id &&
                    $package->package_type === 'pppoe'
                ) {
                    $router = $hotspotUser->router;

                    if (
                        !$router ||
                        (int) $router->admin_id !== (int) $admin->id
                    ) {
                        $result = [
                            'success' => false,
                            'message' =>
                                'No valid PPPoE router belonging to this ISP Admin was found.',
                        ];
                    } else {
                        $result = $mikrotik->disablePppoeUser(
                            $hotspotUser->username,
                            $router
                        );
                    }
                } else {
                    $result = $mikrotik->disableHotspotUser(
                        $hotspotUser
                    );
                }

                $routerSyncFailed = !(
                    $result['success'] ?? false
                );

                $hotspotUser->update([
                    'status' => 'inactive',
                    'router_synced' => !$routerSyncFailed,
                    'last_sync_status' => $routerSyncFailed
                        ? 'Failed'
                        : 'Success',
                    'last_sync_message' =>
                        $result['message']
                        ?? (
                            $routerSyncFailed
                                ? 'Router synchronization failed during customer suspension.'
                                : 'Router access disabled successfully.'
                        ),
                ]);
            }
        }

        AuditHelper::log(
            'Suspend Customer',
            'Customers',
            $customer->name.' was suspended by ISP Admin.'
        );

        NotificationHelper::create(
            'Customer Suspended',
            $customer->name.' has been suspended.',
            'warning',
            'admin',
            $admin->id
        );

        if ($routerSyncFailed) {
            return back()->with(
                'warning',
                'Customer suspended locally, but router access could not be confirmed as disabled. Check router synchronization.'
            );
        }

        return back()->with(
            'success',
            'Customer suspended and associated service access disabled successfully.'
        );
    }

    /**
     * Reactivate the customer account only.
     *
     * Service is intentionally NOT restored automatically. If the customer
     * has a still-valid suspended subscription, the ISP Admin must activate
     * that subscription separately.
     */
    public function activate(User $customer)
    {
        $admin = $this->currentAdmin();

        $this->ensureCustomerBelongsToAdmin(
            $customer,
            $admin->id
        );

        if (!$customer->is_suspended) {
            return back()->with(
                'success',
                'This customer account is already active.'
            );
        }

        $customer->update([
            'is_suspended' => false,
        ]);

        AuditHelper::log(
            'Activate Customer',
            'Customers',
            $customer->name.' account was reactivated by ISP Admin.'
        );

        NotificationHelper::create(
            'Customer Activated',
            $customer->name.' account has been reactivated.',
            'success',
            'admin',
            $admin->id
        );

        $validSuspendedSubscriptionExists = Subscription::query()
            ->where('user_id', $customer->id)
            ->where('status', 'suspended')
            ->where('start_time', '<=', now())
            ->where('end_time', '>', now())
            ->exists();

        if ($validSuspendedSubscriptionExists) {
            return back()->with(
                'warning',
                'Customer account reactivated. Their suspended subscription remains offline until you activate it from Subscription Management.'
            );
        }

        return back()->with(
            'success',
            'Customer account reactivated successfully. No internet subscription was activated automatically.'
        );
    }

    /**
     * Generate a RouterOS username that is unique within local credentials.
     */
    private function generateNetworkUsername(User $customer): string
    {
        $base = 'user'.$customer->id;
        $username = $base;
        $counter = 1;

        while (
            HotspotUser::query()
                ->where('username', $username)
                ->exists()
        ) {
            $username = $base.'-'.$counter;
            $counter++;
        }

        return $username;
    }

    /**
     * Return the authenticated ISP Admin.
     */
    private function currentAdmin(): User
    {
        $user = auth()->user();

        if (!$user) {
            abort(401);
        }

        if ($user->role !== 'admin') {
            abort(403);
        }

        return $user;
    }

    /**
     * Confirm that a customer belongs to the authenticated ISP Admin.
     */
    private function ensureCustomerBelongsToAdmin(
        User $customer,
        int $adminId
    ): void {
        if (
            $customer->role !== 'customer' ||
            (int) $customer->admin_id !== $adminId
        ) {
            abort(403);
        }
    }
}