<?php

namespace Database\Factories;

use App\Models\PersonalInfo;
use Illuminate\Database\Eloquent\Factories\Factory;

class PersonalInfoFactory extends Factory
{
    protected $model = PersonalInfo::class;

    public function definition()
    {
        return [
            'UserID' => null, // Will be set in the seeder
            'Name' => $this->faker->name,
            'Email' => $this->faker->unique()->safeEmail,
            'PhoneNumber' => $this->faker->phoneNumber,
            'UserAddress' => $this->faker->address
        ];
    }
}
