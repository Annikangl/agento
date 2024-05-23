<?php

declare(strict_types=1);

namespace App\MoonShine\Components;

use App\Models\User\MoonshineUser;
use App\Models\User\Promocode;
use Closure;
use Illuminate\Contracts\View\View;
use MoonShine\Components\MoonShineComponent;

/**
 * @method static static make()
 */
final class PromocodeList extends MoonShineComponent
{
    protected string $view = 'admin.components.promocode-list';

    public function __construct(public MoonshineUser $user)
    {
    }

    protected function viewData(): array
    {
        return [
            'promocodes' => Promocode::query()
                ->where('supervisor_id', $this->user->id)
                ->latest()
                ->get(),
        ];
    }
}
