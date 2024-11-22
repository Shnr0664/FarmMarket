<?php

namespace Database\Factories;

use App\Models\Farmer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Farm>
 */
class FarmFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $cropTypes = [
            ['wheat', 'corn', 'rice'],
            ['tomatoes', 'potatoes', 'onions'],
            ['grapes', 'oranges', 'apples'],
            ['lentils', 'chickpeas', 'soybeans'],
            ['sunflower', 'canola', 'groundnut'],
            ['cotton', 'jute', 'hemp'],
            ['basil', 'mint', 'turmeric'],
        ];

        return [
            'farmer_id' => Farmer::factory(),
            'farm_name' => $this->faker->company,
            'farm_size' => $this->faker->randomFloat(2, 1, 100), // Random size in acres
            'crops_types' => json_encode($this->faker->randomElement($cropTypes)), // Randomly select a crop type
        ];
    }
}
