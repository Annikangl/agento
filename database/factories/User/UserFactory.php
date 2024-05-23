<?php

namespace Database\Factories\User;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'country' => fake()->country(),
            'phone' => $this->faker->randomElement([
                fake()->unique()->phoneNumber(),
                null
            ]),
            'email' => fake()->unique()->email(),
            'password' => bcrypt('password'), // password
            'remember_token' => Str::random(10),
            'fcm_token' => $this->faker->randomElement([
                $this->faker->sha256(),
                null
            ]),
            'device_name' => $this->faker->userAgent(),
        ];
    }
}
