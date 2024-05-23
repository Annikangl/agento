<?php

namespace App\Pipes\User;

use App\Exceptions\Api\User\CreateBalanceException;
use App\Models\User\User;
use App\UseCases\Account\BalanceService;

class CreateBalance
{
    public function __construct(private readonly BalanceService $service)
    {
    }

    /**
     * @throws CreateBalanceException
     */
    public function handle(User $user, \Closure $next)
    {
        $this->service->createEmptyBalance($user);

        return $next($user);
    }
}
