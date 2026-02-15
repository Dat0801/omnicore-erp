<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role as SpatieRole;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    public function configure(): static
    {
        return $this->afterCreating(function (\App\Models\User $user): void {
            // If a legacy 'role' attribute was provided, mirror it into Spatie roles
            $roleValue = null;
            if (isset($user->getAttributes()['role']) && $user->role !== null) {
                $roleValue = is_string($user->role) ? $user->role : ($user->role->value ?? null);
            }

            // Default to 'admin' for tests that expect unrestricted access
            $roleToAssign = $roleValue ?: 'admin';
            SpatieRole::firstOrCreate(['name' => $roleToAssign, 'guard_name' => 'web']);
            $user->syncRoles([$roleToAssign]);
        });
    }

    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
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
     * Indicate that the user is an admin.
     */
    public function admin(): static
    {
        return $this->afterCreating(function (\App\Models\User $user): void {
            SpatieRole::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
            $user->syncRoles(['admin']);
        });
    }

    /**
     * Indicate that the user is a staff member.
     */
    public function staff(): static
    {
        return $this->afterCreating(function (\App\Models\User $user): void {
            SpatieRole::firstOrCreate(['name' => 'staff', 'guard_name' => 'web']);
            $user->syncRoles(['staff']);
        });
    }
}
