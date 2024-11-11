<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use ApiResponse;

    public function register(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email|unique:User',
                'password' => 'required|min:8',
                'user_type' => 'required|in:buyer,farmer', 
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
            ]);

            $user = new User();
            $user->Email = $request->email;
            $user->Password = $request->password;
            $user->save();

            $user->personalInfo()->create([
                'FirstName' => $request->first_name,
                'LastName' => $request->last_name,
            ]);

            switch ($request->user_type) {
                case 'buyer':
                    $user->buyer()->create([]);
                    break;
                case 'farmer':
                    $user->farmer()->create([]);
                    break;
                default:
                    return $this->error('Invalid user type', 400);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return $this->success([
                'user' => $user->load('personalInfo'),
                'token' => $token
            ], 'Registration successful', 201);

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

            $user = User::where('Email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->Password)) {
                return $this->error('Invalid credentials', 401);
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