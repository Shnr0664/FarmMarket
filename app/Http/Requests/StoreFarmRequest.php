<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFarmRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->farmer !== null;
    }

    public function rules(): array
    {
        return [
            'farm_name' => 'required|string|max:255',
            'farm_size' => 'required|numeric|min:0',
            'crops_types' => 'required|array',
            'crops_types.*' => 'string'
        ];
    }
}
