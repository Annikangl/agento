<?php

declare(strict_types=1);

namespace App\MoonShine\Components;

use App\Models\User\Subscription;
use App\Models\User\User;
use Closure;
use Illuminate\Contracts\View\View;
use MoonShine\Components\MoonShineComponent;

/**
 * @method static static make()
 */
final class TransactionList extends MoonShineComponent
{
    protected string $view = 'admin.components.transaction-list';

    public function __construct(public User $user)
    {

    }

    protected function viewData(): array
    {
        return [
            'transactions' => Subscription::with('plan')
                ->where('user_id', $this->user->id)
                ->orderByDesc('is_active')
                ->get(),
        ];
    }
}
