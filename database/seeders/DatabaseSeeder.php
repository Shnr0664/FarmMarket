<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            UserSeeder::class,
            PersonalInfoSeeder::class,
            BuyerSeeder::class,
            FarmerSeeder::class,
            FarmSeeder::class,
            ProductSeeder::class,
            OrderSeeder::class,
            PaymentSeeder::class,
            DeliverySeeder::class,
            CartSeeder::class,
            AdminSeeder::class,
        ]);
    }
}

