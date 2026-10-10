<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * Generic test users default to ISP Admin accounts because Admins are
     * valid standalone users and must not have an admin_id.
     *
     * Customers must always be created through the customer() state so that
     * they are assigned to a valid ISP Admin.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => 'admin',
            'admin_id' => null,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Create a Super Admin account.
     *
     * Super Admins are platform-level users and therefore cannot belong
     * to an ISP Admin.
     */
    public function superAdmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'super_admin',
            'admin_id' => null,
        ]);
    }

    /**
     * Create an ISP Admin account.
     *
     * ISP Admins own their tenant data and therefore cannot themselves
     * belong to another ISP Admin.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
            'admin_id' => null,
        ]);
    }

    /**
     * Create a customer belonging to an ISP Admin.
     *
     * If no Admin is supplied, the factory creates a valid ISP Admin
     * automatically. This preserves the database ownership invariant.
     */
    public function customer(?User $admin = null): static
    {
        return $this->state(function (array $attributes) use ($admin) {
            $owner = $admin;

            if ($owner === null) {
                $owner = User::factory()->admin()->create();
            }

            if ($owner->role !== 'admin') {
                throw new \InvalidArgumentException(
                    'Customer factory state requires a user with the admin role.'
                );
            }

            return [
                'role' => 'customer',
                'admin_id' => $owner->id,
            ];
        });
    }

    /**
     * Create a customer belonging to a specific ISP Admin ID.
     *
     * This state is useful when the owning Admin already exists but only
     * the database identifier is available.
     */
    public function forAdmin(int $adminId): static
    {
        return $this->state(function (array $attributes) use ($adminId) {
            $admin = User::query()
                ->whereKey($adminId)
                ->where('role', 'admin')
                ->first();

            if ($admin === null) {
                throw new \InvalidArgumentException(
                    'The supplied admin ID does not belong to a valid ISP Admin.'
                );
            }

            return [
                'role' => 'customer',
                'admin_id' => $admin->id,
            ];
        });
    }
}