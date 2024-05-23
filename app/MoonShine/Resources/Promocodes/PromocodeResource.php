<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Promocodes;

use App\Models\User\Promocode;
use App\MoonShine\Resources\MoonShineUserResource;
use App\MoonShine\Resources\User\UserResource;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use MoonShine\Decorations\Block;
use MoonShine\Enums\PageType;
use MoonShine\Exceptions\FieldException;
use MoonShine\Fields\Date;
use MoonShine\Fields\ID;
use MoonShine\Fields\Number;
use MoonShine\Fields\Relationships\BelongsTo;
use MoonShine\Fields\Select;
use MoonShine\Fields\Text;
use MoonShine\Models\MoonshineUser;
use MoonShine\Resources\ModelResource;

class PromocodeResource extends ModelResource
{
    protected string $model = Promocode::class;

    protected bool $createInModal = true;

    protected string $title = 'Промокоды';

    protected array $with = ['supervisor'];

    protected ?PageType $redirectAfterSave = PageType::INDEX;

    /**
     * @throws FieldException
     */
    public function fields(): array
    {
        return [
            Block::make([
                ID::make()->sortable(),
                BelongsTo::make('Супервайзер', 'supervisor', resource: new MoonShineUserResource())
                    ->required(),
                Text::make(label: 'Промокод', column: 'code')
                    ->hideOnCreate(),
                Text::make(label: 'Сгенерированный промокод', column: 'code')
                    ->setValue(Promocode::generateUniquePromoCode())
                    ->placeholder('Здесь должен появится промокод')
                    ->readonly()
                    ->hideOnIndex(),
                Select::make('Скидка, %')
                    ->options(Promocode::getPercantageDiscount()),
                BelongsTo::make('Супервайзер', 'supervisor', resource: new UserResource())
                    ->hideOnCreate(),
                Number::make('Использовано уже', 'used_count')
                    ->hideOnCreate(),
                Number::make('Лимит использований', 'usage_limit')
                    ->placeholder('Введите количество допустимых использований промокода'),
                Date::make('Дата истечения', 'expired_at'),

            ]),
        ];
    }


    public function rules(Model $item): array
    {
        return [];
    }

    private function getUser(): MoonshineUser|Authenticatable
    {
        return auth('moonshine')->user();
    }

    public function getActiveActions(): array
    {
        return ['index', 'view', 'delete', 'create'];
    }
}
