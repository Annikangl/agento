<?php

namespace App\Pipes\User;

use App\Exceptions\Api\Subscriptions\CreateSubscriptionException;
use App\Models\User\Plan;
use App\Models\User\User;
use App\UseCases\SubscriptionService;

class GiveSubscription
{
    public function __construct(private readonly SubscriptionService $service)
    {
    }

    /**
     * @throws CreateSubscriptionException
     */
    public function handle(User $user, \Closure $next)
    {
        $plan = Plan::getFreePlan();

        $this->service->createTrial($user, $plan);

        return $next($user);
    }
}
