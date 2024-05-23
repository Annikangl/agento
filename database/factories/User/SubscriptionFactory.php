<?php

namespace Database\Factories\User;

use App\Models\User\Plan;
use App\Models\User\Subscription;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

    public function definition(): array
    {
        $plan = Plan::query()->inRandomOrder()->first();

        return [
            'user_id' => User::query()->inRandomOrder()->value('id'),
            'plan_id' => $plan->id,
            'created_at' => Carbon::now(),
            'expired_at' => Carbon::now()->addDays($plan->duration),
            'is_active' => $this->faker->boolean(),
        ];
    }
}
