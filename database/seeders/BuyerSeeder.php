<?php

namespace Database\Seeders;


use App\Models\Buyer;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BuyerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $userIds = User::pluck('id')->toArray();
        foreach ($userIds as $userId) {
            Buyer::factory(5)->create([
                'user_id' => $userId, // Associate with an existing User ID
            ]);
        }
    }
}
