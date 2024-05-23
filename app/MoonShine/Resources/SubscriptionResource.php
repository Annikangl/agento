<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Enums\SubscriptionEventType;
use App\Models\User\Plan;
use App\Models\User\Subscription;
use Illuminate\Database\Eloquent\Model;
use MoonShine\Fields\Date;
use MoonShine\Fields\Enum;
use MoonShine\Fields\Number;
use MoonShine\Fields\Relationships\BelongsTo;
use MoonShine\Fields\Select;
use MoonShine\Fields\Text;
use MoonShine\Resources\ModelResource;
use MoonShine\Decorations\Block;
use MoonShine\Fields\ID;

class SubscriptionResource extends ModelResource
{
    protected string $model = Subscription::class;

    protected string $title = 'Subscriptions';

    protected array $with = ['plan', 'user'];

    public function indexFields(): array
    {
        return [
            Block::make([
                ID::make()->sortable(),
                BelongsTo::make(
                    label: 'План',
                    relationName: 'plan',
                    resource: new PlanResource()
                ),
                Number::make('ID транзакции', 'original_transaction_id'),
                BelongsTo::make('Пользователь', 'user'),
                Enum::make('Событие', 'event_type')->attach(SubscriptionEventType::class),
                Date::make('Дата операции', 'created_at'),
                Date::make('Дата окончания подписки', 'expired_at'),
            ]),
        ];
    }

    public function detailFields(): array
    {
        return array_merge($this->indexFields(), [
            Text::make('Страна', 'country_code'),
            Text::make('Валюта покупки', 'currency')->hideOnIndex(),
            Text::make('Цена в KZT', 'price_in_purchased_currency'),
            Text::make('Магазин', 'store'),
        ]);
    }

    public function filters(): array
    {
        return [
            Select::make('Магазин', 'store')->options([
                'APP_STORE' => 'App Store',
                'GOOGLE_PLAY' => 'Google Play'
            ])->nullable()->placeholder('Выберите магазин покупки'),
            BelongsTo::make(
                label: 'План',
                relationName: 'plan',
                resource: new PlanResource()
            )->nullable()->placeholder('Выберите план подписки'),

            Date::make('Дата', 'created_at'),
        ];
    }

    public function rules(Model $item): array
    {
        return [];
    }

    public function getActiveActions(): array
    {
        return ['view'];
    }
}
