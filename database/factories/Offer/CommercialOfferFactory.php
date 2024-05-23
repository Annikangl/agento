<?php

namespace Database\Factories\Offer;

use App\Models\Offer\CommercialOffer;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class CommercialOfferFactory extends Factory
{
    protected $model = CommercialOffer::class;

    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->value('id'),
            'source_link' => $this->faker->url(),
            'source_name' => $this->faker->randomElement([
                'property',
                'dubizzle',
                'bayut',
            ]),
            'lang' => $this->faker->randomElement([
                'ru',
                'en'
            ]),
            'title' => $this->faker->word(),
            'status' => $status =$this->faker->randomElement([
                CommercialOffer::STATUS_COMPLETED,

            ]),
            'pdf_path' => $status === CommercialOffer::STATUS_ERROR || $status === CommercialOffer::STATUS_PENDING ?
                null :
                $this->faker->url(),
            'price' => $this->faker->randomElement([
                $this->faker->randomNumber(),
                null
            ]),
            'created_at' => Carbon::now()->subDays(7),
            'updated_at' => Carbon::now(),
            'description' => $this->faker->randomElement([
                $this->faker->realText(),
                null
            ]),
            'hide_description' => $this->faker->boolean(),
            'display_source_link' => $this->faker->boolean(),
        ];
    }
}
