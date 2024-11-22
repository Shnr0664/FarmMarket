<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $email = $this->faker->unique()->safeEmail;
        return [
            'email' => $email,
            'password' => bcrypt('pass1234'), // Default password
            'profile_pic' => $this->generateAvatar($email),
            'remember_token' => Str::random(10),
        ];
    }
    /**
     * Generate a random avatar URL using DiceBear API.
     *
     * @param string $identifier
     * @return string
     */
    private function generateAvatar(string $identifier): string
    {
        // Use the updated DiceBear API
        return "https://api.dicebear.com/6.x/adventurer/svg?seed=" . urlencode($identifier);
    }
}
