<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\User;

use App\Models\User\Promocode;
use Illuminate\Database\Eloquent\Model;
use MoonShine\Fields\Field;
use MoonShine\Fields\Preview;
use MoonShine\Resources\ModelResource;

/**
 * @extends ModelResource<Promocode>
 */
class PromocodeResource extends ModelResource
{
    protected string $model = Promocode::class;

    protected string $title = 'Промокоды пользователей';

    /**
     * @return Field
     */
    public function fields(): array
    {
        return [
            Preview::make('Промокод', 'code'),
            Preview::make('Скидка', 'discount'),
        ];
    }


    /**
     * @param Promocode $item
     * @return array<string, string[]|string>
     */
    public function rules(Model $item): array
    {
        return [];
    }

    public function getActiveActions(): array
    {
        return ['view'];
    }
}
