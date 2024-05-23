<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Enums\CommercialOfferStatus;
use App\Models\Offer\CommercialOffer;
use App\Models\User\User;
use App\MoonShine\Resources\User\UserResource;
use Illuminate\Database\Eloquent\Model;
use MoonShine\ActionButtons\ActionButton;
use MoonShine\Decorations\Block;
use MoonShine\Fields\Date;
use MoonShine\Fields\Enum;
use MoonShine\Fields\Field;
use MoonShine\Fields\ID;
use MoonShine\Fields\Preview;
use MoonShine\Fields\Relationships\BelongsTo;
use MoonShine\Fields\Text;
use MoonShine\Resources\ModelResource;

class CommercialOfferResource extends ModelResource
{
    protected string $model = CommercialOffer::class;

    protected string $title = 'История формирования КП';

    protected array $with = ['user'];

    protected bool $simplePaginate = true;


    public function fields(): array
    {
        return [
            Block::make([
                ID::make()->sortable(),
                BelongsTo::make('Пользователь', 'user', resource: new UserResource())
                    ->badge(fn($status, Field $field) => 'purple'),
                Preview::make(label: 'Источник', formatted: fn($item) => 'Источник')
                    ->link(fn($link, Field $field) => $field->getData()->source_link ?? '#', blank: true),
                Text::make('Заголовок', 'title'),
                Enum::make('Статус', 'status')->attach(CommercialOfferStatus::class),
                Date::make('Создан', 'created_at')->format('d.m.y H:i')
            ]),
        ];
    }

    public function filters(): array
    {
        return [
            Enum::make('Статус КП', 'status')
                ->attach(CommercialOfferStatus::class)
                ->nullable()
                ->placeholder('Выберите статус КП'),

            BelongsTo::make(
                label: 'Пользователь',
                relationName: 'user',
                formatted: fn(User $user) => $user->name
            )
                ->nullable()
                ->placeholder('Введите имя пользователя')
                ->asyncSearch('name', 5),

            Date::make('Дата создания', 'created_at')
                ->changeFill(fn(CommercialOffer $offer, Field $field) => $offer->created_at),
        ];
    }

    public function rules(Model $item): array
    {
        return [];
    }

    public function getActiveActions(): array
    {
        return ['view', 'delete'];
    }

    public function indexButtons(): array
    {
        $resource = new CommercialOfferResource();
        return [
            ActionButton::make('PDF', static fn(CommercialOffer $offer): string => $offer->pdf_path ?? '#')
                ->icon('heroicons.magnifying-glass')
                ->blank()
        ];
    }
}
