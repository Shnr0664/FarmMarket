<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\Order;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $orderIds = Order::pluck('OrderID')->toArray();

        // Create Payment records for each Order
        foreach ($orderIds as $orderId) {
            Payment::factory()->create([
                'OrderID' => $orderId, // Associate with an existing Order ID
            ]);
        }
    }
}
