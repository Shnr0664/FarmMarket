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
use App\Notifications\PasswordResetCodeNotification;
use App\Notifications\PasswordChangedNotification;
use Illuminate\Support\Facades\DB;



class AuthController extends Controller
{
    use ApiResponse;

    public function register(StoreUserRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $emailSeed = urlencode($validated['email']);


        if (empty($validated['profile_pic']) || $validated['profile_pic'] == 'null') {


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

        // Generate and send verification code
        $user->generateVerificationCode();
        $user->notify(new \App\Notifications\SendVerificationCode($user->email_verification_code));


        return response()->json([
            'status' => 'success',
            'message' => 'User registered successfully. Please verify your email.',
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

        // Check if the user is not verified
        if (!$user->hasVerifiedEmail()) {
            Auth::logout();

            return response()->json([
                'status' => 'error',
                'message' => 'Your email address is not verified. Please verify your email to proceed.',
            ], 403);
        }

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


    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $email = $request->email;

        DB::table('password_resets')->where('email', $email)->delete();

        $code = rand(100000, 999999); // Generate a 6-digit code

        // Store the code in the database
        DB::table('password_resets')->updateOrInsert(
            ['email' => $email],
            [
                'code' => $code,
                'created_at' => now(),
                'expires_at' => now()->addMinutes(10), // Code valid for 10 minutes
            ]
        );

        // Send the code to the user
        $user = User::where('email', $email)->first();
        $user->notify(new PasswordResetCodeNotification($code));

        return response()->json(['message' => 'Password reset code sent.']);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'code' => 'required|digits:6',
            'password' => 'required|min:8|confirmed',
        ]);

        $reset = DB::table('password_resets')
            ->where('email', $request->email)
            ->where('code', $request->code)
            ->first();

        if (!$reset) {
            return response()->json(['message' => 'Invalid reset code.'], 400);
        }

        if (now()->greaterThan($reset->expires_at)) {
            return response()->json(['message' => 'Reset code has expired.'], 400);
        }

        // Update the user's password
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Delete the reset record
        DB::table('password_resets')->where('email', $request->email)->delete();

        // Notify the user
        $user->notify(new PasswordChangedNotification());


        return response()->json(['message' => 'Password reset successfully.']);
    }

}
