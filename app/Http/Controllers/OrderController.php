<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Buyer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function listOrders(Request $request): JsonResponse
    {
        $user = $request->user(); // Authenticated user

        if ($user->isAdmin()) {
            // Admin can view all orders
            $orders = Order::with('buyer')->get();
        } else {
            // Regular user can only view their orders
            $buyer = Buyer::where('user_id', $user->id)->first();

            if (!$buyer) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Buyer profile not found',
                ], 404);
            }

            $orders = Order::where('buyer_id', $buyer->id)->get();
        }

        return response()->json([
            'status' => 'success',
            'orders' => $orders,
        ]);
    }
    public function createOrder(Request $request): JsonResponse
    {
        $user = $request->user(); // Authenticated user
        $buyer = Buyer::where('user_id', $user->id)->first();

        if (!$buyer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Buyer profile not found',
            ], 404);
        }

        // Retrieve cart items for the buyer
        $cartItems = Cart::where('buyer_id', $buyer->id)->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cart is empty. Nothing to order.',
            ], 400);
        }

        // Calculate total amount
        $totalAmount = $cartItems->sum('total_amount');

        try {
            // Create the order
            $order = Order::create([
                'buyer_id' => $buyer->id,
                'order_date' => now(), // Set order date as current date/time
                'total_amount' => $totalAmount,
                'order_status' => 'Pending', // Initial status
            ]);

            // Clear the cart for the buyer
            foreach ($cartItems as $item) {
                Cart::where('buyer_id', $item->buyer_id)
                    ->where('product_id', $item->product_id)
                    ->delete();
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Order created successfully',
                'total_amount' => $totalAmount,
                'order_status' => 'Pending',
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create order',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function processOrder(Request $request, $orderId): JsonResponse
    {
        $user = $request->user(); // Authenticated user
        $buyer = Buyer::where('user_id', $user->id)->first();

        if (!$buyer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Buyer profile not found',
            ], 404);
        }

        // Retrieve the order
        $order = Order::where('id', $orderId)->where('buyer_id', $buyer->id)->first();

        if (!$order) {
            return response()->json([
                'status' => 'error',
                'message' => 'Order not found',
            ], 404);
        }

        if ($order->order_status !== 'Pending') {
            return response()->json([
                'status' => 'error',
                'message' => 'Order cannot be processed',
            ], 400);
        }

        // Update order status and order date
        $order->update([
            'order_status' => 'Processing',
            'order_date' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Order processed successfully',
            'order_status' => $order->order_status,
            'order_date' => $order->order_date,
        ]);
    }

    public function cancelOrder(Request $request, $orderId): JsonResponse
    {
        $user = $request->user(); // Authenticated user
        $buyer = Buyer::where('user_id', $user->id)->first();

        if (!$buyer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Buyer profile not found',
            ], 404);
        }

        // Retrieve the order
        $order = Order::where('id', $orderId)
            ->where('buyer_id', $buyer->id)
            ->first();

        if (!$order) {
            return response()->json([
                'status' => 'error',
                'message' => 'Order not found',
            ], 404);
        }

        if ($order->order_status === 'Cancelled') {
            return response()->json([
                'status' => 'error',
                'message' => 'Order is already cancelled',
            ], 400);
        }

        // Update the order status and order date
        $order->update([
            'order_status' => 'Cancelled',
            'order_date' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Order cancelled successfully',
            'order' => $order,
        ]);
    }

    public function completeOrder(Request $request, $orderId): JsonResponse
    {
        $user = $request->user(); // Authenticated user
        $buyer = Buyer::where('user_id', $user->id)->first();

        if (!$buyer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Buyer profile not found',
            ], 404);
        }

        // Retrieve the order
        $order = Order::where('id', $orderId)
            ->where('buyer_id', $buyer->id)
            ->first();

        if (!$order) {
            return response()->json([
                'status' => 'error',
                'message' => 'Order not found',
            ], 404);
        }

        if ($order->order_status !== 'Processing') {
            return response()->json([
                'status' => 'error',
                'message' => 'Only processing orders can be completed',
            ], 400);
        }

        // Update the order status and order date
        $order->update([
            'order_status' => 'Completed',
            'order_date' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Order completed successfully',
            'order' => $order,
        ]);
    }


}