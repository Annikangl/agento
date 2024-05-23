<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Selections;

use App\Enums\Selection\CompletionEnum;
use App\Enums\Selection\DealTypeEnum;
use App\Enums\Selection\PropertyTypeEnum;
use App\Models\Selection\Selection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\ComponentAttributeBag;
use MoonShine\Fields\Date;
use MoonShine\Fields\DateRange;
use MoonShine\Fields\Enum;
use MoonShine\Fields\Field;
use MoonShine\Fields\ID;
use MoonShine\Fields\Preview;
use MoonShine\Fields\Relationships\BelongsTo;
use MoonShine\Fields\Relationships\HasMany;
use MoonShine\Fields\Switcher;
use MoonShine\Resources\ModelResource;

/**
 * @extends ModelResource<Selection>
 */
class SelectionResource extends ModelResource
{
    protected string $model = Selection::class;

    protected string $title = 'Пользовательские подборки';

    protected array $with = ['user', 'adverts'];

    protected bool $simplePaginate = true;

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return array
     */
    public function indexFields(): array
    {
        return [
            ID::make()->sortable(),
            BelongsTo::make('Автор', 'user')->badge(fn($status, Field $field) => 'purple'),
            Preview::make(
                'Кол-во обхявлений',
                formatted: fn(Selection $selection) => $selection->adverts->count()
            ),
            Preview::make('Заголовок', 'title'),
            Switcher::make('Понравилось', 'is_liked'),
            Date::make('Дата создания', 'created_at'),
        ];
    }

    /**
     * @return array
     */
    public function formFields(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function detailFields(): array
    {
        return array_merge($this->indexFields(), [
            Date::make('Дата истечения', 'expired_at'),
            Enum::make('Тип сделки', 'deal_type')->attach(DealTypeEnum::class),
            Enum::make('Тип недвижимости', 'property_type')->attach(PropertyTypeEnum::class),
            Enum::make('Завершение', 'completion')->attach(CompletionEnum::class),
            Preview::make(
                'Кол-во комнат',
                'beds',
                formatted: fn(Selection $selection) => implode(', ', $selection->beds)
            ),
            Preview::make(
                'Площадь от/до',
                formatted: fn(Selection $selection) => $selection->size_from . ' - ' . $selection->size_to
            ),
            Preview::make(
                'Бюджет от/до',
                formatted: fn(Selection $selection) => $selection->budget_from . ' - ' . $selection->budget_to . ' AED'
            ),
            Preview::make('Местоположение', 'location'),
            Preview::make('Ссылка на подборку', 'web_link')
                ->link(fn($link, Field $field) => $link, fn($name, Field $field) => 'Открыть', blank: true),
            HasMany::make('Объявления', 'adverts', resource: new AdvertResource()),
        ]);
    }

    public function filters(): array
    {
        return [
            BelongsTo::make('Автор', 'user')->asyncSearch(
                asyncSearchCount:10
            ),
            Switcher::make('Есть лайк', 'is_liked'),
            DateRange::make('Дата создания', 'created_at'),
        ];
    }

    /**
     * @param Selection $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    public function rules(Model $item): array
    {
        return [];
    }

    public function getActiveActions(): array
    {
        return ['view', 'delete', 'massDelete'];
    }
}
