<?php

namespace Database\Seeders;

use App\Models\Farm;
use App\Models\Farmer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FarmSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Get all Farmer IDs
        $farmerIds = Farmer::pluck('FarmerID')->toArray();

        // Create Farm records for each Farmer
        foreach ($farmerIds as $farmerId) {
            Farm::factory()->create([
                'FarmerID' => $farmerId, // Associate with an existing Farmer ID
                'FarmName' => fake()->company,
                'FarmSize' => fake()->randomFloat(2, 1, 100),
                'CropsTypes' => fake()->words(3, true)
            ]);
        }
    }
}
