<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Buyer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run()
    {
        $buyerIds = Buyer::pluck('BuyerID')->toArray();
        foreach ($buyerIds as $buyerId) {
            Order::factory()->create([
                'BuyerID' => $buyerId,
            ]);
        }
    }
}
