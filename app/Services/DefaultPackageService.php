<?php

namespace App\Services;

use App\Models\Package;
use App\Models\User;
use InvalidArgumentException;

class DefaultPackageService
{
    /**
     * Return the standard starter package definitions.
     *
     * This is the single source of truth for starter packages used by:
     * - newly registered ISP Admins
     * - the package seeder for existing ISP Admins
     *
     * Every package has a finite duration stored exclusively in minutes.
     * Unlimited refers only to internet speed, never package duration.
     *
     * These starter packages use configured speed limits. ISP Admins remain
     * free to edit pricing, speeds, descriptions, router profiles, package
     * types, durations, and availability from Package Management.
     */
    public function definitions(): array
    {
        return [
            [
                'name' => '1 Hour',
                'price' => 10.00,
                'duration_minutes' => 60,
                'is_speed_unlimited' => false,
                'speed' => '5M/5M',
                'download_speed' => '5M',
                'upload_speed' => '5M',
                'package_type' => 'hotspot',
                'router_profile' => '1hour',
                'description' => '1 hour hotspot internet package.',
                'is_active' => true,
            ],
            [
                'name' => '2 Hours',
                'price' => 20.00,
                'duration_minutes' => 120,
                'is_speed_unlimited' => false,
                'speed' => '5M/5M',
                'download_speed' => '5M',
                'upload_speed' => '5M',
                'package_type' => 'hotspot',
                'router_profile' => '2hours',
                'description' => '2 hour hotspot internet package.',
                'is_active' => true,
            ],
            [
                'name' => '3 Hours',
                'price' => 35.00,
                'duration_minutes' => 180,
                'is_speed_unlimited' => false,
                'speed' => '5M/5M',
                'download_speed' => '5M',
                'upload_speed' => '5M',
                'package_type' => 'hotspot',
                'router_profile' => '3hours',
                'description' => '3 hour hotspot internet package.',
                'is_active' => true,
            ],
        ];
    }

    /**
     * Create missing starter packages for an ISP Admin.
     *
     * Existing packages are never overwritten. A starter package is skipped
     * when the same ISP Admin already has either its name or router profile.
     *
     * This protects ISP-specific customization and makes the operation safe
     * to execute repeatedly.
     */
    public function createMissingForAdmin(User $admin): int
    {
        if ($admin->role !== 'admin') {
            throw new InvalidArgumentException(
                'Starter packages can only be created for ISP Admin accounts.'
            );
        }

        $createdCount = 0;

        foreach ($this->definitions() as $definition) {
            $alreadyExists = Package::query()
                ->where('admin_id', $admin->id)
                ->where(function ($query) use ($definition) {
                    $query
                        ->where(
                            'name',
                            $definition['name']
                        )
                        ->orWhere(
                            'router_profile',
                            $definition['router_profile']
                        );
                })
                ->exists();

            if ($alreadyExists) {
                continue;
            }

            Package::create([
                'admin_id' => $admin->id,
                ...$definition,
            ]);

            $createdCount++;
        }

        return $createdCount;
    }
}