<?php

namespace Database\Factories;

use App\Models\Farm;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Farm>
 */
class FarmFactory extends Factory
{
    protected $model = Farm::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'FarmerID' => null, // Will be set in the seeder
            'FarmName' => $this->faker->company,
            'FarmSize' => $this->faker->randomFloat(2, 1, 100),
            'CropsTypes' => $this->faker->words(3, true)
        ];
    }
}
