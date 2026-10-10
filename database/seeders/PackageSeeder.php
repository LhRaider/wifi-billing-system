<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\DefaultPackageService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PackageSeeder extends Seeder
{
    /**
     * Create missing standard starter packages for existing ISP Admins.
     *
     * Starter package definitions are maintained exclusively by
     * DefaultPackageService so registration and database seeding always use
     * the same package configuration.
     */
    public function run(
        DefaultPackageService $defaultPackageService
    ): void {
        User::query()
            ->where('role', 'admin')
            ->orderBy('id')
            ->chunkById(
                100,
                function ($admins) use (
                    $defaultPackageService
                ): void {
                    foreach ($admins as $admin) {
                        DB::transaction(
                            function () use (
                                $admin,
                                $defaultPackageService
                            ): void {
                                $defaultPackageService
                                    ->createMissingForAdmin($admin);
                            }
                        );
                    }
                }
            );
    }
}