<?php

namespace Database\Factories;

use App\Models\Buyer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'buyer_id' => Buyer::factory(),
            'order_date' => $this->faker->date(),
            'total_amount' => $this->faker->randomFloat(2, 10, 500),
            'order_status' => $this->faker->randomElement(['Pending', 'Processing', 'Completed', 'Cancelled']),
        ];
    }
}
