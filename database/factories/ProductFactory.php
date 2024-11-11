<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'FarmID' => null, // Will be set in the seeder
            'ProductName' => $this->faker->word,
            'ProductQuantity' => $this->faker->numberBetween(1, 100),
            'ProductCategory' => $this->faker->word,
            'ProductDesc' => $this->faker->sentence,
            'ProductPrice' => $this->faker->randomFloat(2, 1, 100),
            'ProductImg' => $this->faker->imageUrl(200, 200, 'food')
        ];
    }
}
