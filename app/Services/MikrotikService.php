<?php

namespace App\Services;

use App\Models\Router;
use App\Models\HotspotUser;
use App\Models\Package;
use RouterOS\Client;
use RouterOS\Query;

class MikrotikService
{
    private function realConnectionEnabled()
    {
        return env('MIKROTIK_REAL_CONNECTION', false) === true
            || env('MIKROTIK_REAL_CONNECTION') === 'true';
    }

    private function getRouterForUser(HotspotUser $user)
    {
        if ($user->router_id) {
            return Router::find($user->router_id);
        }

        return Router::where('is_active', true)->first();
    }

    private function getClient(Router $router)
    {
        return new Client([
            'host' => $router->ip_address,
            'user' => $router->username,
            'pass' => $router->password,
            'port' => (int) ($router->api_port ?? 8728),
            'timeout' => 5,
        ]);
    }

    private function rateLimit(Package $package)
    {
        return ($package->upload_speed ?? '1M') . '/' . ($package->download_speed ?? '1M');
    }

    public function testConnection(Router $router)
    {
        if (!$router) {
            return ['success' => false, 'message' => 'Router not found.'];
        }

        if (!$router->is_active) {
            return ['success' => false, 'message' => 'Router is inactive.'];
        }

        if (!$this->realConnectionEnabled()) {
            return [
                'success' => true,
                'message' => 'Simulation mode: router connection simulated successfully for ' . $router->name . '.',
            ];
        }

        try {
            $client = $this->getClient($router);
            $query = new Query('/system/resource/print');
            $client->query($query)->read();

            return [
                'success' => true,
                'message' => 'Real MikroTik connection successful for ' . $router->name . '.',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'MikroTik connection failed: ' . $e->getMessage(),
            ];
        }
    }

    public function createHotspotProfile(Package $package, Router $router)
    {
        if (!$this->realConnectionEnabled()) {
            return [
                'success' => true,
                'message' => 'Simulation mode: Hotspot profile ' . $package->router_profile . ' synced with speed ' . $this->rateLimit($package) . '.',
            ];
        }

        try {
            $client = $this->getClient($router);

            $existing = $client->query(
                (new Query('/ip/hotspot/user/profile/print'))
                    ->where('name', $package->router_profile)
            )->read();

            if (!empty($existing)) {
                $query = (new Query('/ip/hotspot/user/profile/set'))
                    ->equal('.id', $existing[0]['.id'])
                    ->equal('rate-limit', $this->rateLimit($package))
                    ->equal('shared-users', '1');
            } else {
                $query = (new Query('/ip/hotspot/user/profile/add'))
                    ->equal('name', $package->router_profile)
                    ->equal('rate-limit', $this->rateLimit($package))
                    ->equal('shared-users', '1');
            }

            $client->query($query)->read();

            return [
                'success' => true,
                'message' => 'Hotspot profile synced: ' . $package->router_profile,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to sync Hotspot profile: ' . $e->getMessage(),
            ];
        }
    }

    public function createPppoeProfile(Package $package, Router $router)
    {
        if (!$this->realConnectionEnabled()) {
            return [
                'success' => true,
                'message' => 'Simulation mode: PPPoE profile ' . $package->router_profile . ' synced with speed ' . $this->rateLimit($package) . '.',
            ];
        }

        try {
            $client = $this->getClient($router);

            $existing = $client->query(
                (new Query('/ppp/profile/print'))
                    ->where('name', $package->router_profile)
            )->read();

            if (!empty($existing)) {
                $query = (new Query('/ppp/profile/set'))
                    ->equal('.id', $existing[0]['.id'])
                    ->equal('rate-limit', $this->rateLimit($package))
                    ->equal('only-one', 'yes');
            } else {
                $query = (new Query('/ppp/profile/add'))
                    ->equal('name', $package->router_profile)
                    ->equal('rate-limit', $this->rateLimit($package))
                    ->equal('only-one', 'yes');
            }

            $client->query($query)->read();

            return [
                'success' => true,
                'message' => 'PPPoE profile synced: ' . $package->router_profile,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to sync PPPoE profile: ' . $e->getMessage(),
            ];
        }
    }

    public function syncPackageProfile(Package $package, Router $router)
    {
        if ($package->package_type === 'pppoe') {
            return $this->createPppoeProfile($package, $router);
        }

        return $this->createHotspotProfile($package, $router);
    }

    public function createHotspotUser(HotspotUser $user)
    {
        $router = $this->getRouterForUser($user);

        if (!$router) {
            return ['success' => false, 'message' => 'No active router found. Please add or activate a router first.'];
        }

        $user->update(['router_id' => $router->id]);

        if (!$this->realConnectionEnabled()) {
            return [
                'success' => true,
                'message' => 'Simulation mode: hotspot user ' . $user->username . ' created on router ' . $router->name . '.',
            ];
        }

        try {
            $client = $this->getClient($router);

            $query = (new Query('/ip/hotspot/user/add'))
                ->equal('name', $user->username)
                ->equal('password', $user->password)
                ->equal('profile', $user->profile)
                ->equal('disabled', 'no');

            $client->query($query)->read();

            return [
                'success' => true,
                'message' => 'Hotspot user ' . $user->username . ' created on MikroTik router ' . $router->name . '.',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to create hotspot user on MikroTik: ' . $e->getMessage(),
            ];
        }
    }

    public function createPppoeUser($username, $password, Package $package, Router $router)
    {
        if (!$this->realConnectionEnabled()) {
            return [
                'success' => true,
                'message' => 'Simulation mode: PPPoE user ' . $username . ' created using profile ' . $package->router_profile . '.',
            ];
        }

        try {
            $client = $this->getClient($router);

            $query = (new Query('/ppp/secret/add'))
                ->equal('name', $username)
                ->equal('password', $password)
                ->equal('service', 'pppoe')
                ->equal('profile', $package->router_profile)
                ->equal('disabled', 'no');

            $client->query($query)->read();

            return [
                'success' => true,
                'message' => 'PPPoE user ' . $username . ' created on MikroTik.',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to create PPPoE user: ' . $e->getMessage(),
            ];
        }
    }

    public function disablePppoeUser($username, Router $router)
    {
        if (!$this->realConnectionEnabled()) {
            return [
                'success' => true,
                'message' => 'Simulation mode: PPPoE user ' . $username . ' disabled.',
            ];
        }

        try {
            $client = $this->getClient($router);

            $users = $client->query(
                (new Query('/ppp/secret/print'))->where('name', $username)
            )->read();

            if (empty($users)) {
                return ['success' => false, 'message' => 'PPPoE user not found on router.'];
            }

            $client->query(
                (new Query('/ppp/secret/set'))
                    ->equal('.id', $users[0]['.id'])
                    ->equal('disabled', 'yes')
            )->read();

            return [
                'success' => true,
                'message' => 'PPPoE user ' . $username . ' disabled.',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to disable PPPoE user: ' . $e->getMessage(),
            ];
        }
    }

    public function enableHotspotUser(HotspotUser $user)
    {
        $router = $this->getRouterForUser($user);

        if (!$router) {
            return ['success' => false, 'message' => 'No active router found. Cannot enable user on router.'];
        }

        if (!$this->realConnectionEnabled()) {
            return [
                'success' => true,
                'message' => 'Simulation mode: hotspot user ' . $user->username . ' enabled on router ' . $router->name . '.',
            ];
        }

        try {
            $client = $this->getClient($router);

            $routerUsers = $client->query(
                (new Query('/ip/hotspot/user/print'))->where('name', $user->username)
            )->read();

            if (empty($routerUsers)) {
                return $this->createHotspotUser($user);
            }

            $client->query(
                (new Query('/ip/hotspot/user/set'))
                    ->equal('.id', $routerUsers[0]['.id'])
                    ->equal('disabled', 'no')
            )->read();

            return [
                'success' => true,
                'message' => 'Hotspot user ' . $user->username . ' enabled on MikroTik router ' . $router->name . '.',
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Failed to enable hotspot user: ' . $e->getMessage()];
        }
    }

    public function disableHotspotUser(HotspotUser $user)
    {
        $router = $this->getRouterForUser($user);

        if (!$router) {
            return ['success' => false, 'message' => 'No active router found. Cannot disable user on router.'];
        }

        if (!$this->realConnectionEnabled()) {
            return [
                'success' => true,
                'message' => 'Simulation mode: hotspot user ' . $user->username . ' disabled on router ' . $router->name . '.',
            ];
        }

        try {
            $client = $this->getClient($router);

            $routerUsers = $client->query(
                (new Query('/ip/hotspot/user/print'))->where('name', $user->username)
            )->read();

            if (empty($routerUsers)) {
                return ['success' => false, 'message' => 'Hotspot user not found on MikroTik router.'];
            }

            $client->query(
                (new Query('/ip/hotspot/user/set'))
                    ->equal('.id', $routerUsers[0]['.id'])
                    ->equal('disabled', 'yes')
            )->read();

            return [
                'success' => true,
                'message' => 'Hotspot user ' . $user->username . ' disabled on MikroTik router ' . $router->name . '.',
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Failed to disable hotspot user: ' . $e->getMessage()];
        }
    }

    public function resetPassword(HotspotUser $user)
    {
        $router = $this->getRouterForUser($user);

        if (!$router) {
            return ['success' => false, 'message' => 'No active router found. Cannot reset password on router.'];
        }

        if (!$this->realConnectionEnabled()) {
            return [
                'success' => true,
                'message' => 'Simulation mode: password for ' . $user->username . ' reset on router ' . $router->name . '.',
            ];
        }

        try {
            $client = $this->getClient($router);

            $routerUsers = $client->query(
                (new Query('/ip/hotspot/user/print'))->where('name', $user->username)
            )->read();

            if (empty($routerUsers)) {
                return ['success' => false, 'message' => 'Hotspot user not found on MikroTik router.'];
            }

            $client->query(
                (new Query('/ip/hotspot/user/set'))
                    ->equal('.id', $routerUsers[0]['.id'])
                    ->equal('password', $user->password)
            )->read();

            return [
                'success' => true,
                'message' => 'Password for ' . $user->username . ' updated on MikroTik router ' . $router->name . '.',
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Failed to reset hotspot password: ' . $e->getMessage()];
        }
    }
}