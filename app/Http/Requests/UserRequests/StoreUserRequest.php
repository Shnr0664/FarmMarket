<?php

namespace App\Http\Requests\UserRequests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'profile_pic' => 'nullable|string',
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'user_address' => 'required|string|max:500',
            'role' => 'required|string|in:buyer,farmer',
            'delivery_preference' => 'required_if:role,buyer|string',
            'buyer_address' => 'required_if:role,buyer|string|max:500',
        ];
    }
}

