<?php

namespace Database\Factories\Catalogs\Property;

use App\Enums\Selection\CompletionEnum;
use App\Enums\Selection\DealTypeEnum;
use App\Enums\Selection\PropertyTypeEnum;
use App\Models\Catalogs\Property\CatalogProperty;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CatalogProperty>
 */
class CatalogPropertyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'active_flag' => $this->faker->boolean(),
            'update_flag' => $this->faker->boolean(),
            'data_add' => now()->getTimestamp(),
            'data_last_update' => now()->getTimestamp(),
            'miss_try_count' => 0,
            'source' => $this->faker->url(),
            'listed_date' => now()->getTimestamp(),
            'title' => $this->faker->words(3, true),
            'price' => $this->faker->numberBetween(40000, 2400000),
            'period' => $this->faker->randomElement([
                'yearly',
                'sell',
            ]),
            'main_photo' => $this->faker->url(),
            'property_type' => $this->faker->randomElement(PropertyTypeEnum::values()),
            'full_location_path' => $this->faker->randomElement([
                'Al Rashidiya, Ajman',
                'Khuzam, Ras Al Khaimah',
                'Mohamed Bin Zayed Centre, Mohamed Bin Zayed City, Abu Dhabi',
                'Dubai Marina Moon, Dubai Marina, Dubai',
                'Hyatt Regency Creek Heights Residences, Dubai Healthcare City, Dubai'
            ]),
            'property_city' => $this->faker->randomElement([
                'Ajman',
                'Abu Dhabi',
                'Dubai',
                'Ras Al Khaimah',
            ]),
            'property_tower' => $this->faker->randomElement([
                'Mohamed Bin Zayed Centre',
            ]),
            'property_community' => $this->faker->randomElement([
                'Al Jazirah Al Hamra',
                'Al Qasimia',
                'Muwaileh Commercial',
                'Sheikh Hamad Bin Abdullah St.',
            ]),
            'property_subcommunity' => $this->faker->randomElement([
                'Al Bandar',
                'Orient Towers',
            ]),
            'bedrooms' => $this->faker->randomElement([
                $this->faker->numberBetween(1,7),
                'Studio',
                '7+'
            ]),
            'bathrooms' => $this->faker->randomElement([
                $this->faker->numberBetween(1,7),
                null,
                '7+'
            ]),
            'size_sqft' => $this->faker->randomNumber(),
            'size_m2' => $this->faker->randomNumber(),
            'geo_lat' => $this->faker->latitude(),
            'geo_lon' => $this->faker->longitude(),
            'reference' => $this->faker->sentence(),
            'description' => $this->faker->realText(),
            'deal_type' => $this->faker->randomElement(DealTypeEnum::values()),
            'amenity_names' => $this->faker->randomElement([
                null,
                $this->faker->sentence()
            ]),
            'completion_type' => $this->faker->randomElement(CompletionEnum::values()),
            'furnished' => $this->faker->randomElement([
                'YES',
                'NO',
                null
            ]),
            'rera' => $this->faker->randomNumber(),
            'get_images' => 0,
        ];
    }
}
