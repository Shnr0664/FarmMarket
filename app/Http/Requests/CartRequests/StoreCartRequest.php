<?php

namespace App\Http\Requests\CartRequests;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

class StoreCartRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $cart = Cart::where('buyer_id', $this->user()->id)
            ->where('product_id', $this->input('product_id'))
            ->first();

        return $cart ? $this->user()->can('create', $cart) : true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ];
    }

    protected function passedValidation()
    {
        $product = Product::findOrFail($this->product_id);

        if ($this->quantity > $product->product_quantity) {
            abort(400, "Not enough stock for product {$product->name}");
        }
    }
}
