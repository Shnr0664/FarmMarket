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
        $categories = [
            'Vegetables' => ['Tomatoes', 'Potatoes', 'Onions', 'Carrots', 'Cucumbers'],
            'Fruits' => ['Apples', 'Bananas', 'Oranges', 'Grapes', 'Pineapples'],
        ];

        $category = $this->faker->randomElement(array_keys($categories));
        $productName = $this->faker->randomElement($categories[$category]);

        // Map product names to image filenames
        $images = [
            'Tomatoes' => 'tomato.jpg',
            'Potatoes' => 'potato.jpg',
            'Onions' => 'onion.jpg',
            'Carrots' => 'carrot.jpg',
            'Cucumbers' => 'cucumber.jpg',
            'Apples' => 'apple.jpg',
            'Bananas' => 'banana.jpg',
            'Oranges' => 'orange.jpg',
            'Grapes' => 'grape.jpg',
            'Pineapples' => 'pineapple.jpg',
        ];

        $imagePath = isset($images[$productName]) ? "images/products/" . $images[$productName] : "images/products/default.jpg";

        return [
            'farm_id' => Farm::factory(),
            'product_name' => $productName,
            'product_quantity' => $this->faker->numberBetween(10, 500),
            'product_category' => $category,
            'product_desc' => $this->faker->sentence,
            'product_price' => $this->faker->randomFloat(2, 1, 100),
            'product_img' => $imagePath,
        ];
    }
}
