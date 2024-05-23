<?php

namespace Database\Factories\User;

use App\Models\User\Balance;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BalanceFactory extends Factory
{
    protected $model = Balance::class;

    public function definition(): array
    {
        return [
            'user_id' => User::query()->inRandomOrder()->value('id'),
            'amount' => $this->faker->numberBetween(0,999),
            'can_withdrawal' => $this->faker->boolean(),
        ];
    }
}
