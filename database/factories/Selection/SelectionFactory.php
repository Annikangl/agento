<?php

namespace Database\Factories\Selection;

use App\Enums\Selection\CompletionEnum;
use App\Enums\Selection\DealTypeEnum;
use App\Enums\Selection\PropertyTypeEnum;
use App\Enums\Selection\SelectionSizeUnit;
use App\Models\Selection\Selection;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Selection>
 */
class SelectionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::query()->inRandomOrder()->value('id'),
            'title' => 'to ' . $this->faker->firstName(),
            'deal_type' => $this->faker->randomElement(DealTypeEnum::values()),
            'property_type' => $this->faker->randomElement(PropertyTypeEnum::values()),
            'completion' => $this->faker->randomElement(CompletionEnum::values()),
            'beds' => $this->faker->randomElement([
                ['Studio', '1BR', '2BR', '3BR', '4BR', '5BR', '6BR+'],
                ['Studio', '1BR'],
                ['2BR', '3BR'],
                ['5BR', '6BR+'],
                ['6BR+'],
            ]),
            'size_from' => $this->faker->numberBetween(1,10),
            'size_to' => $this->faker->numberBetween(10,60),
            'size_units' => $this->faker->randomElement(SelectionSizeUnit::values()),
            'location' => $this->faker->address(),
            'budget_from' => $this->faker->numberBetween(500000, 10000000),
            'budget_to' => $this->faker->numberBetween(500000, 10000000),
            'is_liked' => $this->faker->boolean(),
            'expired_at' => now()->addDays(Selection::EXPIRED_DAYS),
        ];
    }
}
