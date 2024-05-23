<?php

declare(strict_types=1);

namespace App\MoonShine\Components;

use App\Models\User\User;
use Closure;
use Illuminate\Contracts\View\View;
use MoonShine\Components\MoonShineComponent;

/**
 * @method static static make()
 */
final class CommercialOffersList extends MoonShineComponent
{
    protected string $view = 'admin.components.commercial-offer-list';

    public function __construct(public User $user)
    {
        //
    }

    protected function viewData(): array
    {
        return [
            'items' => $this->user->commercialOffers->sortByDesc('id')->take(25),
        ];
    }
}
