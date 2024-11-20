<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use App\Models\Farmer;
use App\Models\Buyer;
use App\Models\PersonalInfo;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FarmerApprovalTest extends TestCase
{
    use RefreshDatabase;

    public function test_buyer_can_login_successfully()
    {
        // Create buyer user
        $user = User::factory()->create([
            'password' => bcrypt('password123')
        ]);

        $user->personalInfo()->create([
            'name' => 'Test Buyer',
            'email' => 'buyer@test.com',
            'phone_number' => '1234567890',
            'user_address' => '123 Buy St'
        ]);

        Buyer::create([
            'user_id' => $user->id,
            'delivery_preference' => 'Standard',
            'buyer_address' => '123 Buy St'
        ]);

        // Attempt login
        $response = $this->postJson('api/login', [
            'email' => 'buyer@test.com',
            'password' => 'password123'
        ]);
        // dd($response->json());
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'user',
                    'userToken'
                ]
            ]);
    }

    public function test_farmer_registration_pending_approval()
    {
        $response = $this->postJson('api/register', [
            'email' => 'farmer@test.com',
            'password' => 'password123',
            'role' => 'farmer',
            'name' => 'Test Farmer',
            'phone_number' => '1234567890',
            'address' => '123 Farm St'
        ]);

        dd($response->json());

        $response->assertStatus(201)
            ->assertJson([
                'status' => 'success',
                'message' => 'Registration successful. Please wait for admin approval.'
            ]);

        $this->assertDatabaseHas('farmers', [
            'IsApproved' => false
        ]);
    }

    public function test_admin_can_approve_farmer()
    {
        // Create admin user
        $adminUser = User::factory()->create([
            'password' => bcrypt('1234567890')
        ]);
        
        $adminUser->personalInfo()->create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'phone_number' => '1234567890',
            'user_address' => '123 Admin St'
        ]);

        Admin::create([
            'user_id' => $adminUser->id
        ]);

        // Create farmer
        $farmerUser = User::factory()->create();
        $farmerUser->personalInfo()->create([
            'name' => 'Farmer',
            'email' => 'farmer@test.com',
            'phone_number' => '1234567890',
            'user_address' => '123 Farm St'
        ]);
        
        $farmer = Farmer::create([
            'user_id' => $farmerUser->id,
            'IsApproved' => false
        ]);

        // Login as admin
        $response = $this->postJson('/api/login', [
            'email' => 'admin@test.com',
            'password' => '1234567890'
        ]);
        // dd($response->json());
        $token = $response->json()['data']['userToken'];

        // Approve farmer
        $response = $this->patchJson("/api/farmers/{$farmer->id}/approve", [], [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200);
        
        $this->assertDatabaseHas('farmers', [
            'id' => $farmer->id,
            'IsApproved' => true
        ]);
    }
}