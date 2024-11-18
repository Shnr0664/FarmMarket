<?php

namespace Database\Factories;

use App\Models\Farm;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'farm_id' => Farm::factory(),
            'product_name' => $this->faker->word,
            'product_quantity' => $this->faker->numberBetween(10, 100),
            'product_category' => $this->faker->randomElement(['Fruit', 'Vegetable', 'Grain']),
            'product_desc' => $this->faker->sentence,
            'product_price' => $this->faker->randomFloat(2, 1, 100),
            'product_img' => $this->faker->imageUrl(640, 480, 'product'),
        ];
    }
}
