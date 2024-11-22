<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequests\StoreUserRequest;
use App\Http\Requests\UserRequests\LoginUserRequest;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use ApiResponse;

    public function register(StoreUserRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = User::create([
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'profile_pic' => $validated['profile_pic'] ?? null,
            'role' => $validated['role'], // Add role to fillable in User model
        ]);

        $user->personalInfo()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'],
            'user_address' => $validated['user_address'],
        ]);

        if ($validated['role'] === 'buyer') {
            $user->buyer()->create([
                'delivery_preference' => $validated['delivery_preference'],
                'buyer_address' => $validated['buyer_address'],
            ]);
        } elseif ($validated['role'] === 'farmer') {
            $user->farmer()->create();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'User registered successfully',
            'user' => $user->load(['personalInfo', 'buyer', 'farmer']),
        ], 201);
    }

    public function login(LoginUserRequest $request): JsonResponse
    {
        $validated = $request->validated();

        if (!Auth::attempt($validated)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials',
            ], 401);
        }

        $user = Auth::user();

        // Check if the user is a farmer and not approved
        if ($user->farmer && !$user->farmer->IsApproved) {
            Auth::logout();

            return response()->json([
                'status' => 'error',
                'message' => 'Your account is pending approval. Please wait for admin approval.',
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login successful',
            'user' => $user->load(['personalInfo', 'buyer', 'farmer']),
            'token' => $token,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        // Revoke the current user's token
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully',
        ], 200);
    }
}
