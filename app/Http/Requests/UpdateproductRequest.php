<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateproductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->farmer !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'farm_id' => 'required|exists:farms,id',
            'product_name' => 'required|string|max:255',
            'product_quantity' => 'required|integer|min:0',
            'product_category' => 'required|string|in:Fruit,Vegetable,Grain',
            'product_desc' => 'required|string',
            'product_price' => 'required|numeric|min:0',
            'product_img' => 'required|longText'
        ];
    }
}
