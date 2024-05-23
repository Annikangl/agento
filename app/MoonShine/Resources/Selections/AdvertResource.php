<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Selections;

use App\Models\Catalogs\Property\CatalogProperty;
use App\Models\Selection\Advert;
use Illuminate\Database\Eloquent\Model;
use MoonShine\Fields\Date;
use MoonShine\Fields\Field;
use MoonShine\Fields\ID;
use MoonShine\Fields\Preview;
use MoonShine\Fields\Relationships\MorphTo;
use MoonShine\Fields\Switcher;
use MoonShine\Resources\ModelResource;

/**
 * @extends ModelResource<Advert>
 */
class AdvertResource extends ModelResource
{
    protected string $model = Advert::class;

    protected string $title = 'Объявления в подборке';

    protected array $with = ['catalogable'];

    protected array $parentRelations = ['selection'];

    /**
     * @return array
     */
    public function indexFields(): array
    {
        return [
            ID::make()->sortable(),
            Switcher::make('Лайк', 'is_liked'),
            Date::make('Дата лайка', 'liked_at'),
        ];
    }

    /**
     * @return array
     */
    public function formFields(): array
    {
        return [
            ID::make()->sortable(),
        ];
    }

    /**
     * @return array
     */
    public function detailFields(): array
    {
        return array_merge($this->indexFields(),[
            MorphTo::make('catalogable')->types([
                CatalogProperty::class => 'title'
            ]),
        ]);
    }

    /**
     * @param Advert $item
     *
     * @return array<string, string[]|string>
     */
    public function rules(Model $item): array
    {
        return [];
    }

    public function getActiveActions(): array
    {
        return ['delete','view'];
    }
}
