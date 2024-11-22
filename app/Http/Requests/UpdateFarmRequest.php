<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFarmRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->farmer->id === $this->farm->farmer_id;
    }

    public function rules(): array
    {
        return [
            'farm_name' => 'sometimes|string|max:255',
            'farm_size' => 'sometimes|numeric|min:0',
            'crops_types' => 'sometimes|array',
            'crops_types.*' => 'string'
        ];
    }
}
