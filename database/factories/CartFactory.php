<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cart>
 */
class CartFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'BuyerID' => null, // Will be set in the seeder
            'ProductID' => null, // Will be set in the seeder
            'TotalAmount' => $this->faker->randomFloat(2, 10, 100),
            'CartItems' => $this->faker->numberBetween(1, 10),
        ];
    }
}
