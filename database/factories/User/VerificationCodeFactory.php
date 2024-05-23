<?php

namespace Database\Factories\User;

use Illuminate\Database\Eloquent\Factories\Factory;

class VerificationCodeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'email' => $this->faker->email(),
            'code' => $this->faker->numberBetween(1000,9999),
            'expired_at' => now()->addMinutes(10),
        ];
    }
}
