<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use ApiResponse;

    public function register(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email|unique:personal_infos,email',
                'password' => 'required|min:8',
                'role' => 'required|in:buyer,farmer',
                'name' => 'required|string|max:255',
                'phone_number' => 'required|string|max:20',
                'address' => 'required|string|max:500'
            ]);

            $user = User::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
    
            $user->personalInfo()->create([
                'name' => $request->name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'user_address' => $request->address,
            ]);
    
            $message = 'Registration successful';
            if ($request->delivery_preference) {
                $delivery_preference = $request->delivery_preference;
            }
            else{
                $delivery_preference = 'default';
            }
            switch ($request->role) {
                case 'buyer':
                    $user->buyer()->create([
                        'delivery_preference' => $request->delivery_preference,
                        'buyer_address' => $request->address,
                    ]);
                    break;
                case 'farmer':
                    $user->farmer()->create([
                        'IsApproved' => false,
                    ]);
                    $message = 'Registration successful. Please wait for admin approval.';
                    break;
                default:
                    return $this->error('Invalid user type', 400);
            }
    
            $token = $user->createToken('auth_token')->plainTextToken;
    
            return $this->success([
                'user' => $user->load('personalInfo'),
                'userToken' => $token,
            ], $message, 201);
        } catch (ValidationException $e) {
            return $this->error($e->errors(), 422);
        } catch (\Exception $e) {
            return $this->error('Registration failed: ' . $e->getMessage(), 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);
    
            $user = User::whereHas('personalInfo', function ($query) use ($request) {
                $query->where('email', $request->email);
            })->first();
    
            if (!$user) {
                return $this->error('User not found', 404);
            }
            
            if (!Hash::check($request->password, $user->password)) {
                return $this->error('Invalid password', 401);
            }
    
            $user->load(['personalInfo', 'buyer', 'farmer']);
            $token = $user->createToken('auth_token')->plainTextToken;
    
            return $this->success([
                'user' => $user,
                'userToken' => $token
            ], 'Login successful');
        } catch (ValidationException $e) {
            return $this->error($e->errors(), 422);
        } catch (\Exception $e) {
            return $this->error('Login failed: ' . $e->getMessage(), 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return $this->success(null, 'Successfully logged out');
        } catch (\Exception $e) {
            return $this->error('Logout failed: ' . $e->getMessage(), 500);
        }
    }

    public function user(Request $request)
    {
        try {
            $user = $request->user()->load(['personalInfo', 'buyer', 'farmer']);
            return $this->success([
                'user' => $user
            ], 'User data retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve user data: ' . $e->getMessage(), 500);
        }
    }
}