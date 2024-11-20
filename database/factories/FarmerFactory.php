<?php

namespace Database\Factories;

use App\Models\Farm;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Farmer>
 */
class FarmerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'FarmerID' => null,
            'IsApproved' => false
        ];
    }

    // State for approved farmers
    public function approved()
    {
        return $this->state(fn (array $attributes) => [
            'IsApproved' => true
        ]);
    }
}
