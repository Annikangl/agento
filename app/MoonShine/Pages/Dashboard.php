<?php

declare(strict_types=1);

namespace App\MoonShine\Pages;

use App\Models\Analytics\AnalyticsAppVisit;
use App\Models\Catalogs\Property\CatalogProperty;
use App\Models\Offer\CommercialOffer;
use App\Models\Selection\Advert;
use App\Models\Selection\Selection;
use App\Models\User\Subscription;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use MoonShine\Decorations\Block;
use MoonShine\Decorations\Divider;
use MoonShine\Decorations\Grid;
use MoonShine\Decorations\Heading;
use MoonShine\Metrics\DonutChartMetric;
use MoonShine\Metrics\LineChartMetric;
use MoonShine\Metrics\ValueMetric;
use MoonShine\Pages\Page;

class Dashboard extends Page
{
    protected string $title = 'Добро пожаловать, Agento';
    protected string $subtitle = 'Панель администрирования приложения Agento';

    public function breadcrumbs(): array
    {
        return [
            '#' => $this->title()
        ];
    }

    public function title(): string
    {
        return $this->title ?: 'Dashboard';
    }

    public function components(): array
    {
        $activeOffersCount = CommercialOffer::active()->count();

        return [
            Heading::make('Статистика по пользователям'),

            Grid::make([
                ValueMetric::make('Всего пользователей')
                    ->value(User::query()->count())
                    ->columnSpan(4)->icon('heroicons.user-group'),

                ValueMetric::make('Посещений за последние 24 часа')
                    ->value(AnalyticsAppVisit::getAllByDay())
                    ->columnSpan(4, 6)
                    ->icon('heroicons.arrow-up-right'),

                ValueMetric::make('Уникальных посещений за последние 24 часа')
                    ->value(AnalyticsAppVisit::getUniqueAppVisitsByDay())
                    ->columnSpan(4, 6)
                    ->icon('heroicons.arrow-up-right'),
            ])->customAttributes(['class' => 'my-5']),

            Heading::make('Статистика по созданным КП'),

            Grid::make([
                ValueMetric::make('Всего успешно созданных КП')
                    ->value(function () {
                        return CommercialOffer::query()
                            ->active()
                            ->count();
                    })->columnSpan(3, 6)->icon('heroicons.document-check'),

                ValueMetric::make('Dubizzle')
                    ->value(function () use ($activeOffersCount) {
                        $dubizzleOffers = CommercialOffer::query()
                            ->active()
                            ->where('source_name', CommercialOffer::TYPE_DUBIZZLE)
                            ->count();

                        if ($dubizzleOffers === 0) {
                            return '0 %';
                        }

                        $dubizzleOffersPercent = $dubizzleOffers * 100 / $activeOffersCount;

                        return number_format($dubizzleOffersPercent, 1, ',') . ' %';
                    })->columnSpan(3, 6)->icon('heroicons.building-library'),

                ValueMetric::make('Propertyfinder')
                    ->value(function () use ($activeOffersCount) {
                        $propertyOffers = CommercialOffer::query()
                            ->active()
                            ->where('source_name', CommercialOffer::TYPE_PROPERTY)
                            ->count();

                        if ($propertyOffers === 0) {
                            return '0 %';
                        }

                        $propertyOffersPercent = $propertyOffers * 100 / $activeOffersCount;

                        return number_format($propertyOffersPercent, 1, ',') . ' %';
                    })->columnSpan(3, 6)->icon('heroicons.building-library'),

                ValueMetric::make('Bayut')
                    ->value(function () use ($activeOffersCount) {
                        $bayutOffers = CommercialOffer::query()
                            ->active()
                            ->where('source_name', CommercialOffer::TYPE_BAYUT)
                            ->count();

                        if ($bayutOffers === 0) {
                            return '0 %';
                        }

                        $bayutOffersPercent = $bayutOffers * 100 / $activeOffersCount;

                        return number_format($bayutOffersPercent, 1, ',') . ' %';
                    })->columnSpan(3, 6)->icon('heroicons.building-library'),

                ValueMetric::make('Неуспешных КП')
                    ->value(function () {
                        return CommercialOffer::query()
                            ->where('status', CommercialOffer::STATUS_ERROR)
                            ->count();
                    })
                    ->columnSpan(3, 6)->icon('heroicons.x-mark'),

                ValueMetric::make('Среднее кол-во успешных КП за последние 30 дней')
                    ->value(function () {
                        $days = 30;
                        $dateFrom = Carbon::now()->subDays($days);
                        $countOffers = CommercialOffer::query()
                            ->where('status', CommercialOffer::STATUS_COMPLETED)
                            ->where('created_at', '>=', $dateFrom)
                            ->count();

                        return ceil($countOffers / $days);
                    })
                    ->columnSpan(3, 6),

                ValueMetric::make('Среднее кол-во успешных КП на пользователя')
                    ->value(function () use ($activeOffersCount) {
                        $totalUsersWithOffers = CommercialOffer::query()
                            ->distinct('user_id')
                            ->where('status', CommercialOffer::STATUS_COMPLETED)
                            ->count('user_id');

                        return $totalUsersWithOffers > 0 ? ceil($activeOffersCount / $totalUsersWithOffers) : 0;
                    })
                    ->columnSpan(3, 6),

            ])->customAttributes(['class' => 'my-5']),

            Grid::make([
                LineChartMetric::make('Динамика создания КП за последние 30 дн.')
                    ->line([
                        'Создано КП' => CommercialOffer::query()
                            ->selectRaw('COUNT(*) as offer_count, DATE(created_at) as created_date')
                            ->active()
                            ->where('created_at', '>=', Carbon::now()->subDays(30))
                            ->groupBy('created_date')
                            ->pluck('offer_count', 'created_date')
                            ->toArray(),
                    ])
                    ->line([
                        'Уникальных посетителей' => AnalyticsAppVisit::getUniqueAppVisitsByDays(30)
                            ->pluck('user_count', 'created_date')
                            ->toArray(),
                    ], '#EC4176')
                    ->line([
                        'Создано подборок' => Selection::query()
                            ->selectRaw('COUNT(*) as selection_count, DATE(created_at) as created_date')
                            ->where('created_at', '>=', Carbon::now()->subDays(30))
                            ->groupBy('created_date')
                            ->pluck('selection_count', 'created_date')
                            ->toArray(),
                    ], '#F59E0B'),
            ]),

            Divider::make(),
            Heading::make('Статистика по каталогам и  подборкам'),

            Grid::make([
                ValueMetric::make('Объявлений в каталоге Propery')
                    ->value(function () {
                        return CatalogProperty::getActiveHasImagesCount();
                    })
                    ->columnSpan(3, 12)->icon('heroicons.document-check'),
            ]),

            Grid::make([
                ValueMetric::make('Всего создано подборок')
                    ->value(function () {
                        return Selection::count();
                    })
                    ->columnSpan(3, 6)->icon('heroicons.document-check'),
                ValueMetric::make('Среднее кол-во подборок на пользователя')
                    ->value(function () {
                        return Selection::getAvgByUser();
                    })->columnSpan(3,6),
                ValueMetric::make('Среднее кол-во объектов в подборке')
                    ->value(function () {
                        return Selection::getAvgAdvers();
                    })->columnSpan(3),
            ])->customAttributes(['class' => 'my-5']),

            Grid::make([
                LineChartMetric::make('Динамика создания подборок за последние 30 дн.')
                    ->line([
                        'Создано подборок' => Selection::getByDays(30)
                            ->pluck('selection_count', 'created_date')
                            ->toArray(),
                    ]),
            ]),

            Divider::make(),

            Heading::make('Новых пользователей за последние 7 дн.'),

            Grid::make([
                LineChartMetric::make('Новых пользователей за последние 7 дней')
                    ->line([
                        'Зарегистрировалось' => User::query()
                            ->selectRaw('COUNT(*) as user_count, DATE(created_at) as created_date')
                            ->where('created_at', '>=', Carbon::now()->subDays(7))
                            ->groupBy('created_date')
                            ->pluck('user_count', 'created_date')
                            ->toArray(),
                    ])->columnSpan(6),

                LineChartMetric::make('Уникальных посещений за последние 7 дней')
                    ->line([
                        'Уникальных посетителей' => AnalyticsAppVisit::getUniqueAppVisitsByWeek()
                            ->pluck('user_count', 'created_date')
                            ->toArray(),
                    ])->columnSpan(6),
            ]),

            Divider::make(),

            Block::make('Популярные устройства пользователей', [
                DonutChartMetric::make('Популярные устройства')
                    ->values(
                        AnalyticsAppVisit::query()
                            ->selectRaw('COUNT(*) as visit_count, device_name')
                            ->groupBy('device_name')
                            ->orderBy('visit_count', 'desc')
                            ->take(5)
                            ->pluck('visit_count', 'device_name')
                            ->toArray()
                    )->columnSpan(10),
            ]),


        ];
    }
}
