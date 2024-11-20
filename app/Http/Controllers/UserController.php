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
        if ($request->user()->cannot('viewAny', User::class)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 403);
        }

        $users = User::with(['personalInfo', 'buyer', 'farmer'])->get();
        return response()->json([
            'status' => 'success',
            'data' => $users
        ]);
    }

    public function updatePersonalInfo(UpdateUserRequest $request, User $user): JsonResponse
    {
        $validated = $request->validated();

        if ($user->personalInfo) {
            try {
                $user->personalInfo->update([
                    'name' => $validated['name'] ?? $user->personalInfo->name,
                    'phone_number' => $validated['phone_number'] ?? $user->personalInfo->phone_number,
                    'user_address' => $validated['user_address'] ?? $user->personalInfo->user_address,
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'An error occurred while updating user info',
                    'error' => $e->getMessage(),
                ], 500);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Personal info not found for this user',
            ], 404);
        }


        if (isset($validated['email'])) {
            $user->update(['email' => $validated['email']]);
            $user->personalInfo->update(['email' => $validated['email']]);
        }

        if (isset($validated['profile_pic'])) {
            $user->update(['profile_pic' => $validated['profile_pic']]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Personal info updated successfully',
            'user' => $user->load(['personalInfo', 'buyer', 'farmer']),
        ]);
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

    public function show(Request $request): JsonResponse
    {
        $user = $request->user(); // Get the authenticated user
        // Load related information for the authenticated user
        $userData = $user->load(['personalInfo', 'buyer', 'farmer']);
        return response()->json([
            'status' => 'success',
            'data' => $userData,
        ]);
    }
}
