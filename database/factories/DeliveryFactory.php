<?php

namespace Database\Factories;

use App\Models\Delivery;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Delivery>
 */
class DeliveryFactory extends Factory
{
    protected $model = Delivery::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'OrderID' => null, // Will be set in the seeder
            'DeliveryLoc' => $this->faker->address,
            'DShipDate' => $this->faker->date,
            'DeliveryStatus' => $this->faker->randomElement(['In Progress', 'Completed', 'Cancelled']),
            'DFinishDate' => $this->faker->date
        ];
    }
}
