<?php

namespace Database\Seeders;

use App\Models\Farmer;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FarmerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Get all User IDs and randomly select a subset (e.g., 5 out of 10)
        $userIds = User::pluck('UserID')->toArray();
        $selectedUserIds = collect($userIds)->random(5); // Adjust the number as needed

        // Create Farmer records for the selected User IDs
        foreach ($selectedUserIds as $userId) {
            Farmer::factory()->create([
                'UserID' => $userId, // Associate with a randomly selected User ID
            ]);
        }
    }
}
