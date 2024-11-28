<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Notifications\SendVerificationCode;
use App\Notifications\EmailVerifiedNotification;


class VerificationController extends Controller
{
    /**
     * Resend the verification code.
     */
    public function resend(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            // Respond with a generic message
            return response()->json(['message' => 'If the email is registered, a verification code has been sent.'], 200);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email is already verified.'], 400);
        }

        // Generate and send verification code
        $user->generateVerificationCode();
        $user->notify(new SendVerificationCode($user->email_verification_code));

        return response()->json(['message' => 'Verification code sent.']);
    }

    /**
     * Verify the email with a code.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'code' => 'required|digits:6',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user->isVerificationCodeValid($request->code)) {
            return response()->json(['message' => 'Invalid or expired verification code.'], 400);
        }

        // Mark email as verified
        $user->markEmailAsVerified();
        $user->clearVerificationCode();

        //if ($user->role === 'buyer') {
        $user->notify(new EmailVerifiedNotification());
        //}

        return response()->json(['message' => 'Email verified successfully.']);
    }
}
