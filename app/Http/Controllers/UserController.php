<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequests\LoginUserRequest;
use App\Http\Requests\UserRequests\StoreUserRequest;
use App\Http\Requests\UserRequests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        if ($request->user()->isAdmin()) {
            $users = User::with(['personalInfo', 'buyer', 'farmer'])->get();

            // Transform the data to include only id, email, and role
            $transformedUsers = $users->map(function ($user) {
                $role = null;
                if ($user->farmer) {
                    $role = 'farmer';
                } elseif ($user->buyer) {
                    $role = 'buyer';
                }

                return [
                    'id' => $user->id,
                    'email' => $user->email,
                    'role' => $role,
                ];
            });

            return response()->json([
                'status' => 'success',
                'data' => $transformedUsers,
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 403);
        }
    }

    public function show(Request $request): JsonResponse
    {
        $user = $request->user();

        $userData = $user->load(['personalInfo', 'buyer', 'farmer']);

        return response()->json([
            'status' => 'success',
            'data' => $userData,
        ]);
    }

    public function register(StoreUserRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $emailSeed = urlencode($validated['email']);

        // Generate a profile picture if none is provided
        if (empty($validated['profile_pic']) || $validated['profile_pic'] == null) {

            $diceBearUrl = "https://api.dicebear.com/6.x/notionists-neutral/svg?seed={$emailSeed}";

            // Fetch and encode the image in base64
            $base64Image = $this->fetchBase64Image($diceBearUrl);

            $validated['profile_pic'] = $base64Image;
        }

        $user = User::create([
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'profile_pic' => $validated['profile_pic'],
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

    public function updatePersonalInfo(UpdateUserRequest $request, User $user): JsonResponse
    {
        $validated = $request->validated();

        try {
            // Check if personal info exists
            if (!$user->personalInfo) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Personal info not found for this user',
                ], 404);
            }

            // Process profile picture if provided as a base64 string
            if (isset($validated['profile_pic']) && !empty($validated['profile_pic'])) {
                $base64Image = $validated['profile_pic'];
//                return response()->json($base64Image);

                // Validate base64 format
                if (!preg_match('/^data:image\/\w+;base64,/', $base64Image)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Invalid profile_pic format. Ensure it is a valid base64 image string.',
                    ], 400);
                }

                // Update the profile_pic field in the user model
                $user->update(['profile_pic' => $base64Image]);
            }

            // Update personal info fields
            $user->personalInfo->update([
                'name' => $validated['name'] ?? $user->personalInfo->name,
                'phone_number' => $validated['phone_number'] ?? $user->personalInfo->phone_number,
                'user_address' => $validated['user_address'] ?? $user->personalInfo->user_address,
            ]);

            // Update email if provided
            if (isset($validated['email'])) {
                $user->update(['email' => $validated['email']]);
                $user->personalInfo->update(['email' => $validated['email']]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Personal info updated successfully',
                'user' => $user->load(['personalInfo', 'buyer', 'farmer']),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while updating user info',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    public function destroy(Request $request, User $user): JsonResponse
    {
        if ($request->user()->cannot('delete', $user)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 403);
        }

        try {
            $user->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'User deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function fetchBase64Image(string $url): string
    {
        try {
            // Fetch the image content
            $imageContents = file_get_contents($url);

            if ($imageContents === false) {
                throw new \Exception("Failed to fetch image from {$url}");
            }

            // Encode the image in base64
            $base64Image = 'data:image/svg+xml;base64,' . base64_encode($imageContents);

            return $base64Image;
        } catch (\Exception $e) {
            return $e;
        }
    }

}
