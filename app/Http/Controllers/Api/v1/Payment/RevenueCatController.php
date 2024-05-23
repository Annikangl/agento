<?php

namespace App\Http\Controllers\Api\v1\Payment;

use App\Exceptions\Api\Subscriptions\CreateSubscriptionException;
use App\Http\Controllers\Controller;
use App\Models\User\Plan;
use App\Models\User\Subscription;
use App\Models\User\User;
use App\UseCases\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RevenueCatController extends Controller
{
    public function __construct(private readonly SubscriptionService $subscriptionService)
    {
    }

    /**
     * @throws CreateSubscriptionException
     */
    public function handleRevenueCatWebhook(Request $request)
    {
        $payload = $request->json()->all();

        $eventType = $payload['event']['type'];
        $event = $payload['event'];

        $user = $this->getUser($event['app_user_id']);

        switch ($eventType) {
            case Subscription::EVENT_TYPE_INITIAL_PURCHASE:
                $plan = $this->getPlan($event['product_id']);
                $this->subscriptionService->createNewSubscription($user, $plan, $event);
                break;

            case Subscription::EVENT_TYPE_INITIAL_RENEWAL:
                $plan = $this->getPlan($event['product_id']);
                $this->subscriptionService->continueSubscription($user, $plan, $event);
                break;

            case Subscription::EVENT_TYPE_CANCELLATION:
            case Subscription::EVENT_TYPE_EXPIRATION:
                $this->subscriptionService->cancelSubscription($user);
                break;
        }

        return response('')->setStatusCode(Response::HTTP_OK);
    }

    private function getUser($userId): User
    {
        return User::findOrFail($userId);
    }

    private function getPlan(string $plan): Plan
    {
        return Plan::query()->where('name', $plan)->firstOrFail();
    }
}
