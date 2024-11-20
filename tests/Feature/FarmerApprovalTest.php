<?php
// tests/Feature/FarmerApprovalTest.php

use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use App\Models\Farmer;
use App\Models\Buyer; 
use App\Http\Controllers\FarmerController;
use App\Models\PersonalInfo;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FarmerApprovalTest extends TestCase
{
    use RefreshDatabase;

    public function test_buyer_can_login_successfully()
    {
        // Create buyer user
        $user = User::factory()->create([
            'Password' => 'password123'
        ]);
        // dd($user);
        $user->personalInfo()->create([
            'Name' => 'Test Buyer',
            'Email' => 'buyer@test.com',
            'PhoneNumber' => '1234567890',
            'UserAddress' => '123 Buy St'
        ]);

        Buyer::create([
            'UserID' => $user->UserID,
            'DeliveryPreference' => 'Standard',
            'BAddress' => '123 Buy St'
        ]);

        // Attempt login
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'buyer@test.com',
            'password' => 'password123'
        ]);

        // dd($response->json());

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'user',
                    'token'
                ],
                'message',
                'status'
            ]);

            
    }

    public function test_farmer_registration_pending_approval()
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'email' => 'farmer@test.com',
            'password' => 'password123',
            'user_type' => 'farmer',
            'name' => 'Test Farmer',
            'phone_number' => '1234567890',
            'address' => '123 Farm St'
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Registration successful. Please wait for admin approval.',
                'status' => 'success'
            ]);

        $this->assertDatabaseHas('Farmer', [
            'IsApproved' => false
        ]);
    }

    public function test_admin_can_approve_farmer()
    {
        // Create admin user
        $adminUser = User::factory()->create([
            'Password' => '1234567890' // Encrypt password here
        ]);
        
        $adminUser->personalInfo()->create([
            'Name' => 'Admin',
            'Email' => 'admin@test.com',
            'PhoneNumber' => '1234567890',
            'UserAddress' => '123 Admin St'
        ]);

        Admin::create([
            'AdminID' => 1,
            'UserID' => $adminUser->UserID
        ]);

        // Create farmer
        $farmerUser = User::factory()->create();
        $farmerUser->personalInfo()->create([
            'Name' => 'Farmer',
            'Email' => 'farmer@test.com',
            'PhoneNumber' => '1234567890',
            'UserAddress' => '123 Farm St'
        ]);
        
        $farmer = Farmer::create([
            'UserID' => $farmerUser->UserID,
            'IsApproved' => false
        ]);

        // Login as admin
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'admin@test.com',
            'password' => '1234567890'
        ]);

        // dd($response->json());
        // $this->assertArrayHasKey('token', $response->json());
        $token = $response->json()['data']['token'];

        // Approve farmer
        $response = $this->patchJson("/api/v1/farmers/{$farmer->FarmerID}/approve", [], [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200);
        
        $this->assertDatabaseHas('Farmer', [
            'FarmerID' => $farmer->FarmerID,
            'IsApproved' => true
        ]);
    }
}