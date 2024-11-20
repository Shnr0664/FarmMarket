<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Delivery>
 */
class DeliveryFactory extends Factory
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
            'delivery_location' => $this->faker->address,
            'delivery_ship_date' => $this->faker->date(),
            'delivery_status' => $this->faker->randomElement(['Delivered', 'Pending', 'Cancelled']),
            'delivery_finish_date' => $this->faker->date(),
        ];
    }
}
