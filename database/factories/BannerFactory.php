<?php

namespace Database\Factories;

use App\Models\Banner;
use Illuminate\Database\Eloquent\Factories\Factory;

class BannerFactory extends Factory
{
    protected $model = Banner::class;

    public function definition(): array
    {
        return [
            'banner' => $this->faker->filePath(),
            'is_active' => $this->faker->boolean(),
        ];
    }
}
