<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\User;

use App\Models\User\Balance;
use Illuminate\Database\Eloquent\Model;
use MoonShine\Decorations\Block;
use MoonShine\Fields\Field;
use MoonShine\Fields\ID;
use MoonShine\Fields\Switcher;
use MoonShine\Fields\Text;
use MoonShine\Resources\ModelResource;

/**
 * @extends ModelResource<Balance>
 */
class BalanceResource extends ModelResource
{
    protected string $model = Balance::class;

    protected string $title = 'Balances';

    /**
     * @return array
     */
    public function fields(): array
    {
        return [
            Block::make([
               Text::make(
                   'Сумма на балансе',
                   'amount',
                   formatted: fn (Balance $balance) => $balance->amount . '$'
               ),
                Switcher::make('Разрешен ли вывод средств', 'can_withdrawal'),
            ]),
        ];
    }

    /**
     * @param Balance $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    public function rules(Model $item): array
    {
        return [];
    }
}
