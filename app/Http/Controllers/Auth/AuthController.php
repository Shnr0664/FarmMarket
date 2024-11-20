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
                'email' => 'required|email|unique:PersonalInfo,Email',
                'password' => 'required|min:8',
                'user_type' => 'required|in:buyer,farmer',
                'name' => 'required|string|max:255',
                'phone_number' => 'required|string|max:255',
                'address' => 'required|string|max:255',
            ]);

            $user = new User();
            $user->Password = bcrypt($request->password);
            $user->save();
    
            $user->personalInfo()->create([
                'Name' => $request->name,
                'Email' => $request->email,
                'PhoneNumber' => $request->phone_number,
                'UserAddress' => $request->address,
            ]);
    
            $message = 'Registration successful';
    
            switch ($request->user_type) {
                case 'buyer':
                    $user->buyer()->create([
                        'DeliveryPreference' => 'default',
                        'BAddress' => $request->address,
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
                'token' => $token,
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
                $query->where('Email', $request->email);
            })->first();
    
            if (!$user) {
                return $this->error('User not found', 404);
            }
            
            $passwordMatches = Hash::check($request->password, $user->Password);
    
            if (!$passwordMatches) {
                return $this->error('Invalid password', 401);
            }
    
            $user->load(['personalInfo', 'buyer', 'farmer']);
            $token = $user->createToken('auth_token')->plainTextToken;
    
            return $this->success([
                'user' => $user,
                'token' => $token
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