<?php

namespace Database\Factories;

use App\Models\Offer;
use App\Models\Buyer;
use App\Models\Farmer;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class OfferFactory extends Factory
{
    protected $model = Offer::class;

    public function definition()
    {
        return [
            'buyer_id' => Buyer::factory(),
            'farmer_id' => Farmer::factory(),
            'product_id' => Product::factory(),
            'offered_price' => $this->faker->randomFloat(2, 10, 100),
            'status' => 'pending',
            'counter_offer_price' => null,
        ];
    }
}