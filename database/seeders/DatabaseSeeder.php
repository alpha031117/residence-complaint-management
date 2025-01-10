<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Complaints;
use App\Models\Residence;
use App\Models\ComplaintAttachment;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Generate 10 users with the default password
        User::factory(10)->create();

        // Generate 1 verified admin user
        User::factory()->verified()->withRole('admin')->create();

        // Complaints::factory(10)->create();

        // // Create 10 complaint attachments using the factory
        // ComplaintAttachment::factory(10)->create();

        Residence::factory(10)->create();

    }
}
