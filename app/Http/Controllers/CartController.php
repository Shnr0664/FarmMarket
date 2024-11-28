<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartRequests\RemoveCartRequest;
use App\Http\Requests\CartRequests\StoreCartRequest;
use App\Models\Buyer;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Notifications\LowStockNotification;
use App\Notifications\NoStockNotification;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function addToCart(StoreCartRequest $request): JsonResponse
    {
        $validated = $request->validated();

        // Retrieve the authenticated user's ID (skip buyer_id in request)
        $userId = $request->user()->id;
        $buyerId = Buyer::where('user_id', $userId)->first()->id;
        //        dd ($buyerId);

        // Fetch product and calculate total for the current addition
        $product = Product::findOrFail($validated['product_id']);
        $productTotal = $product->product_price * $validated['quantity'];

        // Check if the cart entry already exists for this buyer and product
        $cart_item = Cart::where('buyer_id', $buyerId)
            ->where('product_id', $validated['product_id'])
            ->first();

        if ($cart_item) {
            Cart::where('buyer_id', $buyerId)
                ->where('product_id', $validated['product_id'])
                ->update([
                    'quantity' => $cart_item->quantity + $validated['quantity'],
                    'total_amount' => $cart_item->total_amount + $productTotal,
                ]);
        } else {
            $cart_item = Cart::create([
                'buyer_id' => $buyerId,
                'product_id' => $validated['product_id'],
                'quantity' => $validated['quantity'],
                'total_amount' => $productTotal,
            ]);
        }
        // Reduce product stock
        $product->update(['product_quantity' => $product->product_quantity - $validated['quantity']]);

        // Notify farmer if stock is low or out of stock
        if ($product->product_quantity > 0 && $product->product_quantity <= 10) {
            if ($product->farm && $product->farm->farmer && $product->farm->farmer->user) {
                $farmer = $product->farm->farmer;
                $farmer->user->notify(new LowStockNotification($product));
            } else {
                \Log::warning('Low stock notification: Farm or farmer is missing for product ID: ' . $product->id);
            }
        }

        if ($product->product_quantity == 0) {
            if ($product->farm->farmer && $product->farm->farmer->user) {
                $farmer = $product->farm->farmer;
                $farmer->user->notify(new NoStockNotification($product));
            } else {
                \Log::warning('No stock notification: Farmer is missing for product ID: ' . $product->id);
            }
        }


        return response()->json([
            'status' => 'success',
            'message' => 'Product added to cart',
        ]);
    }


    public function viewCart(Request $request): JsonResponse
    {
        $user = $request->user();
        $buyer = Buyer::where('user_id', $user->id)->first();

        if (!$buyer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Buyer profile not found',
            ], 404);
        }

        $cart = Cart::where('buyer_id', $buyer->id)
            ->with('product') // Ensure you have a `product` relationship on the `Cart` model
            ->get();

        if ($cart->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Your cart is empty',
            ], 404);
        }

        if ($user->cannot('view', $cart->first())) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 403);
        }

        // Transform the cart data to match the expected structure
        $cartItems = $cart->map(function ($item) {
            return [
                'id' => $item->product_id,
                'name' => $item->product->product_name,
                'image' => $item->product->product_img, // Assuming the product has an `image` property
                'price' => (float) $item->product->product_price, // Assuming the product has a `price` property
                'quantity' => $item->quantity,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => [
                'cartItems' => $cartItems,
            ],
        ]);
    }

    public function removeFromCart(RemoveCartRequest $request): JsonResponse
    {
        $validated = $request->validated();

        // Retrieve the authenticated user's ID (skip buyer_id in request)
        $userId = $request->user()->id;
        $buyerId = Buyer::where('user_id', $userId)->first()->id;

        // Fetch product to validate stock changes
        $product = Product::findOrFail($validated['product_id']);

        // Check if the cart entry exists for this buyer and product
        $cart_item = Cart::where('buyer_id', $buyerId)
            ->where('product_id', $validated['product_id'])
            ->first();

        if (!$cart_item) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found in cart',
            ], 404);
        }

        // Calculate the updated quantity
        $newQuantity = max(0, $cart_item->quantity - $validated['quantity']);

        $cart_test = Cart::where('buyer_id', $buyerId)
            ->where('product_id', $validated['product_id']);

        if ($newQuantity > 0) {
            // Update the cart entry
            $cart_test
                ->update([
                    'quantity' => $newQuantity,
                    'total_amount' => $newQuantity * $product->product_price,
                ]);
        } else {
            // Remove the product from the cart if quantity reaches zero
            $cart_test
                ->delete();
        }

        // Restore product stock
        $stockToRestore = min($validated['quantity'], $cart_item->quantity);
        $product->update(['product_quantity' => $product->product_quantity + $stockToRestore]);

        return response()->json([
            'status' => 'success',
            'message' => $newQuantity > 0
                ? 'Product quantity updated in cart'
                : 'Product removed from cart',
        ]);
    }


}
