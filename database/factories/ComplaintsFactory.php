<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\ComplaintAttachment;
use App\Models\Residence;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Complaints>
 */
class ComplaintsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Create the ComplaintAttachment first
        $attachment = ComplaintAttachment::factory()->create();
        return [
            'issued_by' => User::factory(),
            'residence_id' => Residence::factory(),
            'complaint_title' => $this->faker->sentence(),
            'complaint_details' => $this->faker->paragraph(),
            'complaint_feedback' => $this->faker->paragraph(),
            'complaint_status' => $this->faker->randomElement(['Pending', 'Resolved', 'In Progress']),
            'file_attachment' => $attachment->id,
            'assigned_to' => User::factory()->state(['role' => 'staff']),
            'resolved_at' => $this->faker->optional()->dateTimeThisYear(),
            'resolution_time' => $this->faker->numberBetween(1, 1000),
            'updated_by' => User::factory(),
        ];
    }
}
