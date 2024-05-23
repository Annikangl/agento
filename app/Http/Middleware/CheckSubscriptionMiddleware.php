<?php

namespace App\Http\Middleware;

use App\Exceptions\Api\Subscriptions\SubscriptionRequredException;
use Closure;
use Illuminate\Http\Request;

class CheckSubscriptionMiddleware
{
    /**
     * @throws SubscriptionRequredException
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && !$user->hasActiveSubscription()) {
            throw new SubscriptionRequredException("You don't have access", 402);
        }

        return $next($request);
    }
}
