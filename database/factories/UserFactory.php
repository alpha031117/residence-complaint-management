<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\Residence;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    /**
     * The current password being used b    y the factory.
     */
    protected static ?string $password = null;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone_number' => $this->faker->phoneNumber(),
            
            // Randomly assign a residence_id from existing residences
            'residence_id' => Residence::factory(), // Automatically links a residence from the factory
            
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'), // Set the default password
            'profile_photo_path' => $this->faker->imageUrl(), // Random image URL
            'role' => $this->faker->randomElement(['user', 'admin', 'staff']), // Random role
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the user has been verified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => now(),
        ]);
    }

    /**
     * Indicate that the user is a specific role (e.g., admin).
     *
     * @param  string  $role
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withRole(string $role): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => $role,
        ]);
    }
}

