<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Enums\Selection\PropertyTypeEnum;
use App\Models\Analytics\AnalyticsAppVisit;
use App\Models\Catalogs\Property\CatalogProperty;
use App\Models\Catalogs\Property\CatalogPropertyImg;
use App\Models\Notification\PushNotification;
use App\Models\Offer\CommercialOffer;
use App\Models\Selection\Selection;
use App\Models\Ticket;
use App\Models\User\Plan;
use App\Models\User\Subscription;
use App\Models\User\User;
use App\UseCases\SubscriptionService;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        PushNotification::factory(50)->create();
//        CatalogProperty::factory()
//            ->has(CatalogPropertyImg::factory()->count(5), 'images')
//            ->create();

//        Selection::factory(500)->create();
//        CommercialOffer::factory(32)->create();
//        $service = app(SubscriptionService::class);
//
//        $users = User::factory(29)->create();
//
//        $plan = Plan::getFreePlan();
//
//        $users->each(function (User $user) use ($plan, $service) {
//            $service->createTrial($user, $plan);
//        });
//
//        AnalyticsAppVisit::factory(200)->create();
    }
}
