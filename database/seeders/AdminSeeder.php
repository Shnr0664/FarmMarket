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
            'AdminID' => 1,
            'UserID' => 11 // UserID for Admin User
        ]);
    }
}
