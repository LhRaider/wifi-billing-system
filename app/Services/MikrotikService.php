<?php

namespace App\Services;

use App\Models\HotspotSession;
use App\Models\HotspotUser;
use App\Models\Package;
use App\Models\Router;
use Illuminate\Support\Facades\Log;
use RouterOS\Client;
use RouterOS\Query;
use Throwable;

class MikrotikService
{
    /**
     * Determine whether real MikroTik communication is enabled.
     */
    private function realConnectionEnabled(): bool
    {
        return filter_var(
            env('MIKROTIK_REAL_CONNECTION', false),
            FILTER_VALIDATE_BOOLEAN
        );
    }

    /**
     * Find a valid active router for a Hotspot user.
     *
     * This resolver is used when access may be granted,
     * updated, or otherwise synchronized as active.
     *
     * A customer must never be connected to a router
     * belonging to another ISP Admin.
     */
    private function getRouterForUser(
        HotspotUser $hotspotUser
    ): ?Router {
        $hotspotUser->loadMissing('user');

        $customer = $hotspotUser->user;

        if (!$customer || !$customer->admin_id) {
            return null;
        }

        /*
         * Prefer the router already assigned to this Hotspot
         * user, but only if it belongs to the customer's Admin
         * and is currently active.
         */
        if ($hotspotUser->router_id) {
            $router = Router::query()
                ->whereKey($hotspotUser->router_id)
                ->where(
                    'admin_id',
                    $customer->admin_id
                )
                ->where('is_active', true)
                ->first();

            if ($router) {
                return $router;
            }
        }

        /*
         * Fallback is strictly restricted to an active router
         * belonging to the same ISP Admin.
         */
        return Router::query()
            ->where(
                'admin_id',
                $customer->admin_id
            )
            ->where('is_active', true)
            ->orderBy('id')
            ->first();
    }

    /**
     * Find the exact assigned router for revoking Hotspot
     * access.
     *
     * Revocation is intentionally different from activation.
     * An inactive router must never be used to grant access,
     * but an assigned router may still need to be contacted
     * after it has been marked inactive locally so an existing
     * RouterOS credential/session can be disabled.
     *
     * No fallback router is allowed for revocation because
     * another router does not own this credential.
     */
    private function getAssignedRouterForRevocation(
        HotspotUser $hotspotUser
    ): ?Router {
        $hotspotUser->loadMissing('user');

        $customer = $hotspotUser->user;

        if (
            !$customer ||
            !$customer->admin_id ||
            !$hotspotUser->router_id
        ) {
            return null;
        }

        return Router::query()
            ->whereKey($hotspotUser->router_id)
            ->where(
                'admin_id',
                $customer->admin_id
            )
            ->first();
    }

    /**
     * Build the RouterOS API client.
     *
     * Router passwords are decrypted automatically by the
     * encrypted cast on the Router model.
     */
    private function getClient(Router $router): Client
    {
        return new Client([
            'host' => $router->ip_address,
            'user' => $router->username,
            'pass' => $router->password,
            'port' => (int) ($router->api_port ?? 8728),
            'timeout' => 5,
        ]);
    }

    /**
     * Build the RouterOS rate-limit value for a package.
     *
     * The package model uses:
     * - upload/download values for limited-speed packages
     * - null speed values for unlimited-speed packages
     *
     * A null return value means that no bandwidth cap should
     * be configured by the application.
     */
    private function rateLimit(Package $package): ?string
    {
        if ($package->is_speed_unlimited) {
            return null;
        }

        $uploadSpeed = trim(
            (string) $package->upload_speed
        );

        $downloadSpeed = trim(
            (string) $package->download_speed
        );

        if (
            $uploadSpeed === '' ||
            $downloadSpeed === ''
        ) {
            return null;
        }

        return $uploadSpeed
            . '/'
            . $downloadSpeed;
    }

    /**
     * Return a safe human-readable speed description.
     */
    private function speedDescription(
        Package $package
    ): string {
        if ($package->is_speed_unlimited) {
            return 'Unlimited';
        }

        return $this->rateLimit($package)
            ?? 'Not Set';
    }

    /**
     * Confirm that a package and router belong to the same
     * ISP Admin.
     */
    private function sameAdmin(
        Package $package,
        Router $router
    ): bool {
        return (int) $package->admin_id
            === (int) $router->admin_id;
    }

    /**
     * Disconnect every active Hotspot session belonging
     * to the supplied username.
     */
    private function disconnectHotspotSessions(
        Client $client,
        string $username
    ): int {
        $sessions = $client->query(
            (new Query('/ip/hotspot/active/print'))
                ->where('user', $username)
        )->read();

        $disconnected = 0;

        foreach ($sessions as $session) {
            if (empty($session['.id'])) {
                continue;
            }

            $client->query(
                (new Query('/ip/hotspot/active/remove'))
                    ->equal(
                        '.id',
                        $session['.id']
                    )
            )->read();

            $disconnected++;
        }

        return $disconnected;
    }

    /**
     * Disconnect every active PPPoE session belonging
     * to the supplied username.
     */
    private function disconnectPppoeSessions(
        Client $client,
        string $username
    ): int {
        $sessions = $client->query(
            (new Query('/ppp/active/print'))
                ->where('name', $username)
        )->read();

        $disconnected = 0;

        foreach ($sessions as $session) {
            if (empty($session['.id'])) {
                continue;
            }

            $client->query(
                (new Query('/ppp/active/remove'))
                    ->equal(
                        '.id',
                        $session['.id']
                    )
            )->read();

            $disconnected++;
        }

        return $disconnected;
    }

    /**
     * Test communication with a MikroTik router.
     */
    public function testConnection(
        Router $router
    ): array {
        if (!$router->is_active) {
            return [
                'success' => false,
                'message' => 'Router is inactive.',
            ];
        }

        if (!$this->realConnectionEnabled()) {
            return [
                'success' => true,
                'message' =>
                    'Simulation mode: router connection simulated successfully for '
                    . $router->name
                    . '.',
            ];
        }

        try {
            $client = $this->getClient($router);

            $client->query(
                new Query('/system/resource/print')
            )->read();

            return [
                'success' => true,
                'message' =>
                    'MikroTik connection successful for '
                    . $router->name
                    . '.',
            ];
        } catch (Throwable $e) {
            Log::error(
                'MikroTik connection failed.',
                [
                    'router_id' => $router->id,
                    'admin_id' => $router->admin_id,
                    'exception' => get_class($e),
                    'error' => $e->getMessage(),
                ]
            );

            return [
                'success' => false,
                'message' =>
                    'Unable to connect to the MikroTik router. Check the router configuration and try again.',
            ];
        }
    }

    /**
     * Create or update a MikroTik Hotspot profile.
     */
    public function createHotspotProfile(
        Package $package,
        Router $router
    ): array {
        if (!$this->sameAdmin(
            $package,
            $router
        )) {
            return [
                'success' => false,
                'message' =>
                    'Package and router belong to different Admins.',
            ];
        }

        if (!$router->is_active) {
            return [
                'success' => false,
                'message' => 'Router is inactive.',
            ];
        }

        if (!$this->realConnectionEnabled()) {
            return [
                'success' => true,
                'message' =>
                    'Simulation mode: Hotspot profile '
                    . $package->router_profile
                    . ' synced with speed '
                    . $this->speedDescription($package)
                    . '.',
            ];
        }

        try {
            $client = $this->getClient($router);

            $existing = $client->query(
                (
                    new Query(
                        '/ip/hotspot/user/profile/print'
                    )
                )->where(
                    'name',
                    $package->router_profile
                )
            )->read();

            $rateLimit = $this->rateLimit(
                $package
            );

            if (!empty($existing)) {
                $query = (
                    new Query(
                        '/ip/hotspot/user/profile/set'
                    )
                )
                    ->equal(
                        '.id',
                        $existing[0]['.id']
                    )
                    ->equal(
                        'rate-limit',
                        $rateLimit ?? ''
                    )
                    ->equal(
                        'shared-users',
                        '1'
                    );
            } else {
                $query = (
                    new Query(
                        '/ip/hotspot/user/profile/add'
                    )
                )
                    ->equal(
                        'name',
                        $package->router_profile
                    )
                    ->equal(
                        'rate-limit',
                        $rateLimit ?? ''
                    )
                    ->equal(
                        'shared-users',
                        '1'
                    );
            }

            $client->query($query)->read();

            return [
                'success' => true,
                'message' =>
                    'Hotspot profile synced successfully.',
            ];
        } catch (Throwable $e) {
            Log::error(
                'Failed to sync MikroTik Hotspot profile.',
                [
                    'package_id' => $package->id,
                    'router_id' => $router->id,
                    'admin_id' => $router->admin_id,
                    'exception' => get_class($e),
                    'error' => $e->getMessage(),
                ]
            );

            return [
                'success' => false,
                'message' =>
                    'Unable to sync the Hotspot profile with the router.',
            ];
        }
    }

    /**
     * Create or update a MikroTik PPPoE profile.
     */
    public function createPppoeProfile(
        Package $package,
        Router $router
    ): array {
        if (!$this->sameAdmin(
            $package,
            $router
        )) {
            return [
                'success' => false,
                'message' =>
                    'Package and router belong to different Admins.',
            ];
        }

        if (!$router->is_active) {
            return [
                'success' => false,
                'message' => 'Router is inactive.',
            ];
        }

        if (!$this->realConnectionEnabled()) {
            return [
                'success' => true,
                'message' =>
                    'Simulation mode: PPPoE profile '
                    . $package->router_profile
                    . ' synced with speed '
                    . $this->speedDescription($package)
                    . '.',
            ];
        }

        try {
            $client = $this->getClient($router);

            $existing = $client->query(
                (
                    new Query(
                        '/ppp/profile/print'
                    )
                )->where(
                    'name',
                    $package->router_profile
                )
            )->read();

            $rateLimit = $this->rateLimit(
                $package
            );

            if (!empty($existing)) {
                $query = (
                    new Query(
                        '/ppp/profile/set'
                    )
                )
                    ->equal(
                        '.id',
                        $existing[0]['.id']
                    )
                    ->equal(
                        'rate-limit',
                        $rateLimit ?? ''
                    )
                    ->equal(
                        'only-one',
                        'yes'
                    );
            } else {
                $query = (
                    new Query(
                        '/ppp/profile/add'
                    )
                )
                    ->equal(
                        'name',
                        $package->router_profile
                    )
                    ->equal(
                        'rate-limit',
                        $rateLimit ?? ''
                    )
                    ->equal(
                        'only-one',
                        'yes'
                    );
            }

            $client->query($query)->read();

            return [
                'success' => true,
                'message' =>
                    'PPPoE profile synced successfully.',
            ];
        } catch (Throwable $e) {
            Log::error(
                'Failed to sync MikroTik PPPoE profile.',
                [
                    'package_id' => $package->id,
                    'router_id' => $router->id,
                    'admin_id' => $router->admin_id,
                    'exception' => get_class($e),
                    'error' => $e->getMessage(),
                ]
            );

            return [
                'success' => false,
                'message' =>
                    'Unable to sync the PPPoE profile with the router.',
            ];
        }
    }

    /**
     * Synchronize the correct RouterOS profile type.
     */
    public function syncPackageProfile(
        Package $package,
        Router $router
    ): array {
        if (!$this->sameAdmin(
            $package,
            $router
        )) {
            return [
                'success' => false,
                'message' =>
                    'Package and router belong to different Admins.',
            ];
        }

        if ($package->package_type === 'pppoe') {
            return $this->createPppoeProfile(
                $package,
                $router
            );
        }

        return $this->createHotspotProfile(
            $package,
            $router
        );
    }

    /**
     * Resolve the RouterOS Hotspot profile from the credential's
     * current subscription package.
     *
     * Existing credentials without an attached subscription keep
     * their stored profile for backwards compatibility. When a
     * subscription is attached, its package becomes authoritative.
     */
    private function resolveHotspotProfile(
        HotspotUser $user,
        Router $router
    ): array {
        $user->loadMissing([
            'user',
            'subscription.package',
        ]);

        $customer = $user->user;
        $subscription = $user->subscription;

        if (!$customer || !$customer->admin_id) {
            return [
                'success' => false,
                'message' =>
                    'The Hotspot credential does not belong to a valid customer.',
            ];
        }

        if (
            (int) $customer->admin_id !== (int) $router->admin_id
        ) {
            return [
                'success' => false,
                'message' =>
                    'The Hotspot customer and router belong to different Admins.',
            ];
        }

        if (!$subscription) {
            $profile = trim((string) $user->profile);

            if ($profile === '') {
                return [
                    'success' => false,
                    'message' =>
                        'The Hotspot credential does not have a RouterOS profile.',
                ];
            }

            return [
                'success' => true,
                'profile' => $profile,
            ];
        }

        $package = $subscription->package;

        if (
            !$package ||
            (int) $subscription->user_id !== (int) $user->user_id ||
            (int) $package->admin_id !== (int) $customer->admin_id ||
            (int) $package->admin_id !== (int) $router->admin_id
        ) {
            return [
                'success' => false,
                'message' =>
                    'The subscription package does not belong to this customer and ISP Admin.',
            ];
        }

        if ($package->package_type !== 'hotspot') {
            return [
                'success' => false,
                'message' =>
                    'The subscription package is not a Hotspot package.',
            ];
        }

        $profile = trim(
            (string) $package->router_profile
        );

        if ($profile === '') {
            return [
                'success' => false,
                'message' =>
                    'The subscription package does not have a RouterOS Hotspot profile.',
            ];
        }

        if ($user->profile !== $profile) {
            $user->update([
                'profile' => $profile,
            ]);

            $user->profile = $profile;
        }

        return [
            'success' => true,
            'profile' => $profile,
        ];
    }

    /**
     * Create or update a MikroTik Hotspot user.
     */
    public function createHotspotUser(
        HotspotUser $user
    ): array {
        $router = $this->getRouterForUser(
            $user
        );

        if (!$router) {
            return [
                'success' => false,
                'message' =>
                    'No active router belonging to this Admin was found.',
            ];
        }

        if (
            (int) $user->router_id
            !== (int) $router->id
        ) {
            $user->update([
                'router_id' => $router->id,
            ]);
        }

        $profileResult = $this->resolveHotspotProfile(
            $user,
            $router
        );

        if (!$profileResult['success']) {
            return $profileResult;
        }

        $profile = $profileResult['profile'];

        if (!$this->realConnectionEnabled()) {
            return [
                'success' => true,
                'message' =>
                    'Simulation mode: hotspot user '
                    . $user->username
                    . ' created or updated and internet access enabled.',
            ];
        }

        try {
            $client = $this->getClient($router);

            $existingUsers = $client->query(
                (
                    new Query(
                        '/ip/hotspot/user/print'
                    )
                )->where(
                    'name',
                    $user->username
                )
            )->read();

            if (!empty($existingUsers)) {
                $client->query(
                    (
                        new Query(
                            '/ip/hotspot/user/set'
                        )
                    )
                        ->equal(
                            '.id',
                            $existingUsers[0]['.id']
                        )
                        ->equal(
                            'password',
                            $user->password
                        )
                        ->equal(
                            'profile',
                            $profile
                        )
                        ->equal(
                            'disabled',
                            'no'
                        )
                )->read();

                return [
                    'success' => true,
                    'message' =>
                        'Hotspot user updated and internet access enabled.',
                ];
            }

            $client->query(
                (
                    new Query(
                        '/ip/hotspot/user/add'
                    )
                )
                    ->equal(
                        'name',
                        $user->username
                    )
                    ->equal(
                        'password',
                        $user->password
                    )
                    ->equal(
                        'profile',
                        $profile
                    )
                    ->equal(
                        'disabled',
                        'no'
                    )
            )->read();

            return [
                'success' => true,
                'message' =>
                    'Hotspot user created and internet access enabled.',
            ];
        } catch (Throwable $e) {
            Log::error(
                'MikroTik Hotspot activation failed.',
                [
                    'hotspot_user_id' => $user->id,
                    'router_id' => $router->id,
                    'admin_id' => $router->admin_id,
                    'exception' => get_class($e),
                    'error' => $e->getMessage(),
                ]
            );

            return [
                'success' => false,
                'message' =>
                    'Unable to activate the Hotspot user on the router.',
            ];
        }
    }

    /**
     * Create or update a MikroTik PPPoE user.
     */
    public function createPppoeUser(
        string $username,
        string $password,
        Package $package,
        Router $router
    ): array {
        if (!$this->sameAdmin(
            $package,
            $router
        )) {
            return [
                'success' => false,
                'message' =>
                    'Package and router belong to different Admins.',
            ];
        }

        if (!$router->is_active) {
            return [
                'success' => false,
                'message' => 'Router is inactive.',
            ];
        }

        if (!$this->realConnectionEnabled()) {
            return [
                'success' => true,
                'message' =>
                    'Simulation mode: PPPoE user '
                    . $username
                    . ' created or updated and internet access enabled.',
            ];
        }

        try {
            $client = $this->getClient($router);

            $existingUsers = $client->query(
                (
                    new Query(
                        '/ppp/secret/print'
                    )
                )->where(
                    'name',
                    $username
                )
            )->read();

            if (!empty($existingUsers)) {
                $client->query(
                    (
                        new Query(
                            '/ppp/secret/set'
                        )
                    )
                        ->equal(
                            '.id',
                            $existingUsers[0]['.id']
                        )
                        ->equal(
                            'password',
                            $password
                        )
                        ->equal(
                            'service',
                            'pppoe'
                        )
                        ->equal(
                            'profile',
                            $package->router_profile
                        )
                        ->equal(
                            'disabled',
                            'no'
                        )
                )->read();

                return [
                    'success' => true,
                    'message' =>
                        'PPPoE user updated and internet access enabled.',
                ];
            }

            $client->query(
                (
                    new Query(
                        '/ppp/secret/add'
                    )
                )
                    ->equal(
                        'name',
                        $username
                    )
                    ->equal(
                        'password',
                        $password
                    )
                    ->equal(
                        'service',
                        'pppoe'
                    )
                    ->equal(
                        'profile',
                        $package->router_profile
                    )
                    ->equal(
                        'disabled',
                        'no'
                    )
            )->read();

            return [
                'success' => true,
                'message' =>
                    'PPPoE user created and internet access enabled.',
            ];
        } catch (Throwable $e) {
            Log::error(
                'MikroTik PPPoE activation failed.',
                [
                    'username' => $username,
                    'package_id' => $package->id,
                    'router_id' => $router->id,
                    'admin_id' => $router->admin_id,
                    'exception' => get_class($e),
                    'error' => $e->getMessage(),
                ]
            );

            return [
                'success' => false,
                'message' =>
                    'Unable to activate the PPPoE user on the router.',
            ];
        }
    }

    /**
     * Enable an existing PPPoE user.
     */
    public function enablePppoeUser(
        string $username,
        Router $router,
        ?Package $package = null,
        ?string $password = null
    ): array {
        if (!$router->is_active) {
            return [
                'success' => false,
                'message' => 'Router is inactive.',
            ];
        }

        if (
            $package &&
            !$this->sameAdmin(
                $package,
                $router
            )
        ) {
            return [
                'success' => false,
                'message' =>
                    'Package and router belong to different Admins.',
            ];
        }

        if (!$this->realConnectionEnabled()) {
            return [
                'success' => true,
                'message' =>
                    'Simulation mode: PPPoE user '
                    . $username
                    . ' enabled.',
            ];
        }

        try {
            $client = $this->getClient($router);

            $users = $client->query(
                (
                    new Query(
                        '/ppp/secret/print'
                    )
                )->where(
                    'name',
                    $username
                )
            )->read();

            if (empty($users)) {
                if (
                    $package &&
                    $password !== null
                ) {
                    return $this->createPppoeUser(
                        $username,
                        $password,
                        $package,
                        $router
                    );
                }

                return [
                    'success' => false,
                    'message' =>
                        'PPPoE user was not found on the router.',
                ];
            }

            $query = (
                new Query('/ppp/secret/set')
            )
                ->equal(
                    '.id',
                    $users[0]['.id']
                )
                ->equal(
                    'disabled',
                    'no'
                );

            if ($package) {
                $query->equal(
                    'profile',
                    $package->router_profile
                );
            }

            if ($password !== null) {
                $query->equal(
                    'password',
                    $password
                );
            }

            $client->query($query)->read();

            return [
                'success' => true,
                'message' =>
                    'PPPoE user enabled successfully.',
            ];
        } catch (Throwable $e) {
            Log::error(
                'Failed to enable PPPoE user.',
                [
                    'username' => $username,
                    'router_id' => $router->id,
                    'admin_id' => $router->admin_id,
                    'exception' => get_class($e),
                    'error' => $e->getMessage(),
                ]
            );

            return [
                'success' => false,
                'message' =>
                    'Unable to enable the PPPoE user on the router.',
            ];
        }
    }

    /**
     * Disable PPPoE credentials and immediately disconnect
     * any currently active PPPoE session.
     *
     * Revocation is allowed against an inactive router because
     * an existing RouterOS credential may still need to be
     * disabled after the router is marked inactive locally.
     */
    public function disablePppoeUser(
        string $username,
        Router $router
    ): array {
        if (!$this->realConnectionEnabled()) {
            return [
                'success' => true,
                'message' =>
                    'Simulation mode: PPPoE user '
                    . $username
                    . ' disabled and active PPPoE session disconnected.',
            ];
        }

        try {
            $client = $this->getClient($router);

            $users = $client->query(
                (
                    new Query(
                        '/ppp/secret/print'
                    )
                )->where(
                    'name',
                    $username
                )
            )->read();

            if (empty($users)) {
                return [
                    'success' => false,
                    'message' =>
                        'PPPoE user was not found on the router.',
                ];
            }

            /*
             * Prevent future authentication first.
             */
            $client->query(
                (
                    new Query(
                        '/ppp/secret/set'
                    )
                )
                    ->equal(
                        '.id',
                        $users[0]['.id']
                    )
                    ->equal(
                        'disabled',
                        'yes'
                    )
            )->read();

            /*
             * Then disconnect sessions that are already active.
             */
            $disconnected =
                $this->disconnectPppoeSessions(
                    $client,
                    $username
                );

            $message =
                'PPPoE user disabled.';

            if ($disconnected > 0) {
                $message .= ' '
                    . $disconnected
                    . ' active session(s) disconnected.';
            } else {
                $message .=
                    ' No active session was connected.';
            }

            return [
                'success' => true,
                'message' => $message,
            ];
        } catch (Throwable $e) {
            Log::error(
                'Failed to disable PPPoE user.',
                [
                    'username' => $username,
                    'router_id' => $router->id,
                    'admin_id' => $router->admin_id,
                    'exception' => get_class($e),
                    'error' => $e->getMessage(),
                ]
            );

            return [
                'success' => false,
                'message' =>
                    'Unable to disable the PPPoE user on the router.',
            ];
        }
    }

    /**
     * Enable an existing Hotspot user.
     */
    public function enableHotspotUser(
        HotspotUser $user
    ): array {
        $router = $this->getRouterForUser(
            $user
        );

        if (!$router) {
            return [
                'success' => false,
                'message' =>
                    'No active router belonging to this Admin was found.',
            ];
        }

        $profileResult = $this->resolveHotspotProfile(
            $user,
            $router
        );

        if (!$profileResult['success']) {
            return $profileResult;
        }

        $profile = $profileResult['profile'];

        if (!$this->realConnectionEnabled()) {
            return [
                'success' => true,
                'message' =>
                    'Simulation mode: hotspot user '
                    . $user->username
                    . ' enabled on router '
                    . $router->name
                    . '.',
            ];
        }

        try {
            $client = $this->getClient($router);

            $routerUsers = $client->query(
                (
                    new Query(
                        '/ip/hotspot/user/print'
                    )
                )->where(
                    'name',
                    $user->username
                )
            )->read();

            if (empty($routerUsers)) {
                return $this->createHotspotUser(
                    $user
                );
            }

            $client->query(
                (
                    new Query(
                        '/ip/hotspot/user/set'
                    )
                )
                    ->equal(
                        '.id',
                        $routerUsers[0]['.id']
                    )
                    ->equal(
                        'password',
                        $user->password
                    )
                    ->equal(
                        'profile',
                        $profile
                    )
                    ->equal(
                        'disabled',
                        'no'
                    )
            )->read();

            return [
                'success' => true,
                'message' =>
                    'Hotspot user enabled successfully.',
            ];
        } catch (Throwable $e) {
            Log::error(
                'Failed to enable Hotspot user.',
                [
                    'hotspot_user_id' => $user->id,
                    'router_id' => $router->id,
                    'admin_id' => $router->admin_id,
                    'exception' => get_class($e),
                    'error' => $e->getMessage(),
                ]
            );

            return [
                'success' => false,
                'message' =>
                    'Unable to enable the Hotspot user on the router.',
            ];
        }
    }

    /**
     * Disable Hotspot credentials and immediately disconnect
     * all active Hotspot sessions for the user.
     *
     * Unlike activation, revocation deliberately uses the
     * exact assigned router even when it has been marked
     * inactive locally. This prevents an inactive-router flag
     * from blocking security-critical access revocation.
     */
    public function disableHotspotUser(
        HotspotUser $user
    ): array {
        $router =
            $this->getAssignedRouterForRevocation(
                $user
            );

        if (!$router) {
            return [
                'success' => false,
                'message' =>
                    'The assigned router belonging to this Admin was not found.',
            ];
        }

        if (!$this->realConnectionEnabled()) {
            return [
                'success' => true,
                'message' =>
                    'Simulation mode: hotspot user '
                    . $user->username
                    . ' disabled and active session disconnected on router '
                    . $router->name
                    . '.',
            ];
        }

        try {
            $client = $this->getClient($router);

            $routerUsers = $client->query(
                (
                    new Query(
                        '/ip/hotspot/user/print'
                    )
                )->where(
                    'name',
                    $user->username
                )
            )->read();

            if (empty($routerUsers)) {
                return [
                    'success' => false,
                    'message' =>
                        'Hotspot user was not found on the assigned router.',
                ];
            }

            /*
             * Prevent future logins first.
             */
            $client->query(
                (
                    new Query(
                        '/ip/hotspot/user/set'
                    )
                )
                    ->equal(
                        '.id',
                        $routerUsers[0]['.id']
                    )
                    ->equal(
                        'disabled',
                        'yes'
                    )
            )->read();

            /*
             * Remove any sessions that are already active.
             */
            $disconnected =
                $this->disconnectHotspotSessions(
                    $client,
                    $user->username
                );

            $message =
                'Hotspot user disabled successfully.';

            if ($disconnected > 0) {
                $message .= ' '
                    . $disconnected
                    . ' active session(s) disconnected.';
            } else {
                $message .=
                    ' No active session was connected.';
            }

            return [
                'success' => true,
                'message' => $message,
            ];
        } catch (Throwable $e) {
            Log::error(
                'Failed to disable Hotspot user.',
                [
                    'hotspot_user_id' => $user->id,
                    'router_id' => $router->id,
                    'admin_id' => $router->admin_id,
                    'exception' => get_class($e),
                    'error' => $e->getMessage(),
                ]
            );

            return [
                'success' => false,
                'message' =>
                    'Unable to disable the Hotspot user on the assigned router.',
            ];
        }
    }

    /**
     * Synchronize active RouterOS Hotspot sessions into local history.
     * Existing sessions are closed only after a successful RouterOS read.
     */
    public function syncHotspotSessions(Router $router): array
    {
        $counts = ['created' => 0, 'updated' => 0, 'closed' => 0, 'ignored' => 0];

        if (!$router->is_active) {
            return array_merge(['success' => false, 'message' => 'Router is inactive.'], $counts);
        }

        if (!$this->realConnectionEnabled()) {
            return array_merge([
                'success' => true,
                'message' => 'Simulation mode: Hotspot session synchronization skipped for ' . $router->name . '.',
            ], $counts);
        }

        try {
            $client = $this->getClient($router);
            $routerSessions = $client->query(new Query('/ip/hotspot/active/print'))->read();

            $hotspotUsers = HotspotUser::query()
                ->with('user')
                ->where('router_id', $router->id)
                ->whereHas('user', function ($query) use ($router) {
                    $query->where('role', 'customer')
                        ->where('admin_id', $router->admin_id);
                })
                ->get()
                ->keyBy('username');

            $seenUserIds = [];

            foreach ($routerSessions as $routerSession) {
                $username = trim((string) ($routerSession['user'] ?? ''));
                $hotspotUser = $username === '' ? null : $hotspotUsers->get($username);

                if (
                    !$hotspotUser ||
                    !$hotspotUser->user ||
                    (int) $hotspotUser->router_id !== (int) $router->id ||
                    (int) $hotspotUser->user->admin_id !== (int) $router->admin_id
                ) {
                    $counts['ignored']++;
                    continue;
                }

                $customerId = (int) $hotspotUser->user_id;
                $ipAddress = $this->nullableRouterValue($routerSession['address'] ?? null);
                $macAddress = $this->nullableRouterValue($routerSession['mac-address'] ?? null);
                $dataUsed = $this->routerCounter($routerSession['bytes-in'] ?? 0)
                    + $this->routerCounter($routerSession['bytes-out'] ?? 0);

                $openSession = HotspotSession::query()
                    ->where('user_id', $customerId)
                    ->where('router_id', $router->id)
                    ->whereNull('logout_time')
                    ->latest('login_time')
                    ->first();

                if (
                    $openSession &&
                    (
                        ($openSession->ip_address !== null && $ipAddress !== null && $openSession->ip_address !== $ipAddress) ||
                        ($openSession->mac_address !== null && $macAddress !== null &&
                            strcasecmp($openSession->mac_address, $macAddress) !== 0)
                    )
                ) {
                    $openSession->update(['logout_time' => now()]);
                    $counts['closed']++;
                    $openSession = null;
                }

                if (!$openSession) {
                    HotspotSession::create([
                        'user_id' => $customerId,
                        'router_id' => $router->id,
                        'mac_address' => $macAddress,
                        'ip_address' => $ipAddress,
                        'login_time' => now(),
                        'logout_time' => null,
                        'data_used' => $dataUsed,
                    ]);
                    $counts['created']++;
                } else {
                    $openSession->update([
                        'mac_address' => $macAddress ?? $openSession->mac_address,
                        'ip_address' => $ipAddress ?? $openSession->ip_address,
                        'data_used' => max((int) $openSession->data_used, $dataUsed),
                    ]);
                    $counts['updated']++;
                }

                $seenUserIds[$customerId] = true;
            }

            $openLocalSessions = HotspotSession::query()
                ->where('router_id', $router->id)
                ->whereNull('logout_time')
                ->whereHas('user', function ($query) use ($router) {
                    $query->where('role', 'customer')
                        ->where('admin_id', $router->admin_id);
                })
                ->get();

            foreach ($openLocalSessions as $localSession) {
                if (isset($seenUserIds[(int) $localSession->user_id])) {
                    continue;
                }

                $localSession->update(['logout_time' => now()]);
                $counts['closed']++;
            }

            return array_merge([
                'success' => true,
                'message' => 'Hotspot sessions synchronized successfully.',
            ], $counts);
        } catch (Throwable $e) {
            Log::error('Failed to synchronize MikroTik Hotspot sessions.', [
                'router_id' => $router->id,
                'admin_id' => $router->admin_id,
                'exception' => get_class($e),
                'error' => $e->getMessage(),
            ]);

            return array_merge([
                'success' => false,
                'message' => 'Unable to synchronize Hotspot sessions with the router.',
            ], $counts);
        }
    }

    private function nullableRouterValue(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function routerCounter(mixed $value): int
    {
        return is_numeric($value) ? max(0, (int) $value) : 0;
    }

    /**
     * Synchronize the Hotspot user's current password
     * with the MikroTik router.
     */
    public function resetPassword(
        HotspotUser $user
    ): array {
        $router = $this->getRouterForUser(
            $user
        );

        if (!$router) {
            return [
                'success' => false,
                'message' =>
                    'No active router belonging to this Admin was found.',
            ];
        }

        if (!$this->realConnectionEnabled()) {
            return [
                'success' => true,
                'message' =>
                    'Simulation mode: password for '
                    . $user->username
                    . ' reset on router '
                    . $router->name
                    . '.',
            ];
        }

        try {
            $client = $this->getClient($router);

            $routerUsers = $client->query(
                (
                    new Query(
                        '/ip/hotspot/user/print'
                    )
                )->where(
                    'name',
                    $user->username
                )
            )->read();

            if (empty($routerUsers)) {
                return [
                    'success' => false,
                    'message' =>
                        'Hotspot user was not found on the router.',
                ];
            }

            $client->query(
                (
                    new Query(
                        '/ip/hotspot/user/set'
                    )
                )
                    ->equal(
                        '.id',
                        $routerUsers[0]['.id']
                    )
                    ->equal(
                        'password',
                        $user->password
                    )
            )->read();

            return [
                'success' => true,
                'message' =>
                    'Hotspot password updated successfully.',
            ];
        } catch (Throwable $e) {
            Log::error(
                'Failed to reset Hotspot password.',
                [
                    'hotspot_user_id' => $user->id,
                    'router_id' => $router->id,
                    'admin_id' => $router->admin_id,
                    'exception' => get_class($e),
                    'error' => $e->getMessage(),
                ]
            );

            return [
                'success' => false,
                'message' =>
                    'Unable to update the Hotspot password on the router.',
            ];
        }
    }
}