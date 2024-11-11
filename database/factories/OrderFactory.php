<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'BuyerID' => null, // Will be set in the seeder
            'OrderDate' => $this->faker->date,
            'TotalAmount' => $this->faker->randomFloat(2, 50, 500),
            'OrderStatus' => $this->faker->randomElement(['Pending', 'Completed', 'Cancelled'])
        ];
    }
}
