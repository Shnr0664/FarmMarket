<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // Correct namespace for DB

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('Admin')->insert([
            'admin_id' => 1,
            'user_id' => 11 // UserID for Admin User
        ]);
    }
}
