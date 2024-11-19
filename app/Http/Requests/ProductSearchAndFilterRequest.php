<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductSearchAndFilterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Allow all users to access this request
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'nullable|string|max:255', // Search by product name
            'category' => 'nullable|string|max:255', // Filter by category
            'farm_location' => 'nullable|string|max:255', // Filter by farm location
            'min_price' => 'nullable|numeric|min:0', // Minimum price for filtering
            'max_price' => 'nullable|numeric|min:0', // Maximum price for filtering
            'sort_by' => 'nullable|string|in:price_asc,price_desc,newest,popularity', // Sorting options
            'page' => 'nullable|integer|min:1', // Pagination: page number
            'per_page' => 'nullable|integer|min:1|max:100', // Pagination: items per page
        ];
    }

    /**
     * Add additional validation logic.
     */
    protected function passedValidation()
    {
        // Ensure `min_price` is not greater than `max_price`
        if ($this->filled('min_price') && $this->filled('max_price') && $this->min_price > $this->max_price) {
            abort(400, 'The minimum price cannot be greater than the maximum price.');
        }
    }
}
