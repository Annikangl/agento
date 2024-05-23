<?php

namespace Database\Factories\Catalogs\Property;

use App\Models\Catalogs\Property\CatalogProperty;
use App\Models\Catalogs\Property\CatalogPropertyImg;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CatalogPropertyImg>
 */
class CatalogPropertyImgFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => CatalogProperty::query()->inRandomOrder()->value('id'),
            'type' => $this->faker->randomElement([
                'Stairs',
                'Bathroom'
            ]),
            'path' => $this->faker->url(),
        ];
    }
}
