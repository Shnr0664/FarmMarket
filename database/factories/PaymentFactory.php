<?php

namespace Database\Factories;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'OrderID' => null, // Will be set in the seeder
            'PaymentMethod' => $this->faker->creditCardType,
            'PaymentDate' => $this->faker->date,
            'TotalAmount' => $this->faker->randomFloat(2, 50, 500)
        ];
    }
}
