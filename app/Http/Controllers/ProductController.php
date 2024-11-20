<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateproductRequest;
use App\Http\Requests\ProductSearchAndFilterRequest;
use App\Models\Product;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::query()
            ->with('farm')
            ->get()
            ->groupBy('product_category');

        // Transform data for the frontend (optional)
        $formattedProducts = $products->flatMap(function ($items, $category) {
            return $items->map(function ($product) use ($category) {
                return [
                    'id' => $product->id,
                    'name' => $product->product_name,
                    'category' => $category,
                    'image' => $product->product_img,
                    'price' => $product->product_price,
                ];
            });
        })->values();


        return response()->json([
            'status' => 'success',
            'data' => $formattedProducts
        ]);
    }

    public function searchAndFilter(ProductSearchAndFilterRequest $request)
    {
        $validated = $request->validated();

        $query = Product::query()
            ->with('farm');

        // Search by product name (if provided)
        // Search by name
        if ($request->filled('name')) {
            $query->where('product_name', 'ILIKE', '%' . $validated['name'] . '%');
        }

        // Search by category
        if ($request->filled('category')) {
            $query->where('product_category', 'ILIKE', $validated['category']);
        }

        // Search by farm location
        if ($request->filled('farm_location')) {
            $query->whereHas('farm', function ($q) use ($validated) {
                $q->where('farm_location', 'ILIKE', '%' . $validated['farm_location'] . '%');
            });
        }

        // Filter by price range
        if ($request->filled('min_price')) {
            $query->where('product_price', '>=', $validated['min_price']);
        }
        if ($request->filled('max_price')) {
            $query->where('product_price', '<=', $validated['max_price']);
        }

        // Apply sorting
        if ($request->filled('sort_by')) {
            switch ($validated['sort_by']) {
                case 'price_asc':
                    $query->orderBy('product_price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('product_price', 'desc');
                    break;
                //    case 'newest':
                //      $query->orderBy('created_at', 'desc');
                //        break;
                //  case 'popularity':
                //    $query->orderBy('popularity', 'desc'); // Assuming a `popularity` column exists
                //  break;
            }
        }
        // Paginate the results
        $perPage = $validated['per_page'] ?? 15; // Default to 15 items per page
        $products = $query->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => $products,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Fetch the product by ID along with its related farm
        $product = Product::with('farm')->find($id);

        // If the product is not found, return a 404 error
        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found',
            ], 404);
        }

        // Prepare the response
        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $product->id,
                'name' => $product->product_name,
                'price' => $product->product_price,
                'quantity' => $product->product_quantity,
                'description' => $product->product_desc,
                'images' => json_decode($product->product_img), // Assuming `product_img` stores JSON for multiple images
                'farm' => [
                    'id' => $product->farm->id,
                    'name' => $product->farm->name,
                    'location' => $product->farm->farm_location,
                ],
            ],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateproductRequest $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }
}