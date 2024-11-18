<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;use function Symfony\Component\String\u;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'payment_method' => $this->faker->randomElement(['Credit Card', 'Cash', 'Bank Transfer']),
            'payment_date' => $this->faker->date(),
            'total_amount' => $this->faker->randomFloat(2, 10, 500),
        ];
    }
}
