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
        $userIds = User::pluck('UserID')->toArray();
        foreach ($userIds as $userId) {
            Buyer::factory()->create([
                'UserID' => $userId, // Associate with an existing User ID
            ]);
        }
    }
}
