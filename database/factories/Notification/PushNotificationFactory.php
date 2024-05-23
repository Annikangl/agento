<?php

namespace Database\Factories\Notification;

use Illuminate\Database\Eloquent\Factories\Factory;

class PushNotificationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'content' => $this->faker->paragraph(),
        ];
    }
}
