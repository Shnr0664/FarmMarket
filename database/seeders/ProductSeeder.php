<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Farm;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Get all Farm IDs
        $farmIds = Farm::pluck('FarmID')->toArray();

        // Create Product records for each Farm
        foreach ($farmIds as $farmId) {
            Product::factory()->create([
                'FarmID' => $farmId, // Associate with an existing Farm ID
            ]);
        }
    }
}
