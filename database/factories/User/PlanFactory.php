<?php

namespace Database\Factories\User;

use App\Models\User\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlanFactory extends Factory
{
    protected $model = Plan::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'price' => $this->faker->randomNumber(),
            'duration' => $this->faker->numberBetween(1,30),
            'description' => $this->faker->text(),
        ];
    }
}
