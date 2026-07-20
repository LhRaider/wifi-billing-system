<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Package;

class PackageSeeder extends Seeder
{
    public function run(): void
    {
        Package::create([
            'name' => '1 Hour Package',
            'price' => 10,
            'duration_hours' => 1,
            'speed' => '5 Mbps',
            'description' => '1 hour internet access',
            'is_active' => true,
        ]);

        Package::create([
            'name' => '3 Hours Package',
            'price' => 20,
            'duration_hours' => 3,
            'speed' => '5 Mbps',
            'description' => '3 hours internet access',
            'is_active' => true,
        ]);

        Package::create([
            'name' => 'Daily Package',
            'price' => 50,
            'duration_hours' => 24,
            'speed' => '10 Mbps',
            'description' => '24 hours internet access',
            'is_active' => true,
        ]);

        Package::create([
            'name' => 'Weekly Package',
            'price' => 250,
            'duration_hours' => 168,
            'speed' => '10 Mbps',
            'description' => '7 days internet access',
            'is_active' => true,
        ]);

        Package::create([
            'name' => 'Monthly Package',
            'price' => 800,
            'duration_hours' => 720,
            'speed' => '20 Mbps',
            'description' => '30 days internet access',
            'is_active' => true,
        ]);
    }
}