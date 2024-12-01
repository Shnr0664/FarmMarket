<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use App\Models\Product;
use App\Models\Message;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    public function submitOffer(Request $request)
    {
        $buyer = auth()->user()->buyer;

        $validated = $request->validate([
            'farmer_id' => 'required|exists:farmers,id',
            'product_id' => 'required|exists:products,id',
            'offered_price' => 'required|numeric|min:0',
            'message' => 'nullable|string',
        ]);

        // Check if the product belongs to the farmer
        $product = Product::where('id', $validated['product_id'])
            ->whereHas('farm', function ($query) use ($validated) {
                $query->where('farmer_id', $validated['farmer_id']);
            })->firstOrFail();

        if ($product->product_quantity <= 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product is sold out',
            ], 400);
        }

        $offer = Offer::create([
            'buyer_id' => $buyer->id,
            'farmer_id' => $validated['farmer_id'],
            'product_id' => $validated['product_id'],
            'offered_price' => $validated['offered_price'],
        ]);

        // Send a message in the chat
        Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $product->farm->farmer->user->id,
            'message' => $validated['message'] ?? 'New offer submitted',
            'offer_id' => $offer->id,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Offer submitted successfully',
            'data' => $offer,
        ]);
    }

    public function respondToOffer(Request $request, $offerId)
    {
        $farmer = auth()->user()->farmer;

        $validated = $request->validate([
            'status' => 'required|in:accepted,rejected,countered',
            'counter_offer_price' => 'nullable|numeric|min:0',
            'message' => 'nullable|string',
        ]);

        $offer = Offer::where('id', $offerId)
            ->where('farmer_id', $farmer->id)
            ->firstOrFail();

        if ($validated['status'] == 'countered' && empty($validated['counter_offer_price'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Counter offer price is required when status is countered',
            ], 422);
        }

        $offer->status = $validated['status'];
        if ($validated['status'] == 'countered') {
            $offer->counter_offer_price = $validated['counter_offer_price'];
        }
        $offer->save();
        $offer->buyer->user->notify(new OfferStatusNotification($offer));

        // Notify the buyer via chat
        Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $offer->buyer->user->id,
            'message' => $validated['message'] ?? 'Offer ' . $validated['status'],
            'offer_id' => $offer->id,
        ]);

        // Optionally, send a notification to the buyer

        return response()->json([
            'status' => 'success',
            'message' => 'Offer ' . $validated['status'],
            'data' => $offer,
        ]);
    }

    public function getOffers(Request $request)
    {
        $user = auth()->user();

        if ($user->buyer) {
            $offers = Offer::where('buyer_id', $user->buyer->id)
                ->with('product', 'farmer.user.personalInfo')
                ->get();
        } elseif ($user->farmer) {
            $offers = Offer::where('farmer_id', $user->farmer->id)
                ->with('product', 'buyer.user.personalInfo')
                ->get();
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 403);
        }

        return response()->json([
            'status' => 'success',
            'data' => $offers,
        ]);
    }
}