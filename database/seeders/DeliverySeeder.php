<?php

namespace Database\Seeders;

use App\Models\Delivery;
use App\Models\Order;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DeliverySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $orderIds = Order::pluck('OrderID')->toArray();

        // Create Delivery records for each Order
        foreach ($orderIds as $orderId) {
            Delivery::factory()->create([ // Correct the method name here
                'OrderID' => $orderId, // Associate with an existing Order ID
            ]);
        }
    }
}
