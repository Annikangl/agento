<?php

namespace App\Http\Controllers\Api\v1\Payment;

use App\Exceptions\Api\Subscriptions\CreateSubscriptionException;
use App\Http\Controllers\Controller;
use App\Models\User\User;
use App\UseCases\SubscriptionService;

class SubscriptionController extends Controller
{
    public function __construct(private readonly SubscriptionService $service)
    {
    }

    /**
     * @throws CreateSubscriptionException
     */
    public function cancelSubscription(User $user)
    {
        $this->service->cancelSubscription($user);

        return redirect()->back();
    }
}
