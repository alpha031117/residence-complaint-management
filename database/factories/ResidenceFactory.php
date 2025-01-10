<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\residence>
 */
class ResidenceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'residence_name' => 'Residence ' . $this->faker->numberBetween(1, 3), // Random residence name: "Residence 1", "Residence 2", "Residence 3"
            'block_no' => $this->faker->buildingNumber(),  // Realistic block number like '10', '52A', etc.
            'unit_no' => $this->faker->numberBetween(1, 50), // Random unit number (1 to 50)
        ];
    }
}
