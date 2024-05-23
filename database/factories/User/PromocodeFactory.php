<?php

namespace Database\Factories\User;

use App\Models\User\Promocode;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Promocode>
 */
class PromocodeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'supervisor_id' => null,
            'user_id' => User::query()->inRandomOrder()->value('id'),
            'code' => Promocode::generateUniquePromoCode(),
            'discount' => Promocode::BASE_DISCOUNT_PERCENT,
            'usage_limit' => null,
            'used_count' => 0,
            'expired_at' => null
        ];
    }
}
