<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Cart;
use App\Models\Buyer;
use App\Models\Product;

class CartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Get all Buyer IDs and Product IDs
        $buyerIds = Buyer::pluck('BuyerID')->toArray();
        $productIds = Product::pluck('ProductID')->toArray();

        // Create Cart records
        foreach ($buyerIds as $buyerId) {
            Cart::factory()->create([
                'BuyerID' => $buyerId,
                'ProductID' => $productIds[array_rand($productIds)], // Randomly select a valid ProductID
            ]);
        }
    }
}
