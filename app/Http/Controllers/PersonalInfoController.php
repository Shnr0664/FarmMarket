<?php

namespace App\Http\Controllers;

use App\Models\PersonalInfo;
use App\Models\User;
use Illuminate\Http\Request;

class PersonalInfoController extends Controller
{
    public function store(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'user_address' => 'required|string|max:500',
        ]);

        $personalInfo = PersonalInfo::create([
            'user_id' => $user->id,
            'name' => $validated['name'],
            'email' => $user->email,
            'phone_number' => $validated['phone_number'],
            'user_address' => $validated['user_address'],
        ]);

        return response()->json([
            'message' => 'Personal information saved successfully',
            'personal_info' => $personalInfo,
        ], 201);
    }
}

