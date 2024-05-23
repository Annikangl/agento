<?php

namespace App\Pipes\User;

use App\Exceptions\Api\Promocode\PromocodeException;
use App\Models\User\User;
use App\UseCases\PromoCodeService;

class GenerateAndCreatePromocode
{
    public function __construct(private readonly PromoCodeService $service)
    {
    }

    /**
     * @throws PromocodeException
     */
    public function handle(User $user, \Closure $next)
    {
        $this->service->create($user);

        return $next($user);
    }
}
