<?php

namespace App\Http\Requests;

use App\Rules\LongText;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->farmer !== null;
    }

    public function rules(): array
    {
        return [
            'product_name' => 'required|string|max:255',
            'product_quantity' => 'required|integer|min:0',
            'product_category' => 'required|string|in:Fruit,Vegetable,Grain',
            'product_desc' => 'required', 'string', new LongText,
            'product_price' => 'required|numeric|min:0',
            'product_img' => 'sometimes', 'string', new LongText,
        ];
    }
}