<?php

namespace App\UseCases;

use App\Enums\SubscriptionEventType;
use App\Exceptions\Api\Subscriptions\CreateSubscriptionException;
use App\Models\User\Plan;
use App\Models\User\Subscription;
use App\Models\User\User;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;

class SubscriptionService
{
    /**
     * @param User $user
     * @param Plan $plan
     * @return void
     * @throws CreateSubscriptionException
     */
    public function createTrial(User $user, Plan $plan): void
    {
        $trialSubscriptionData = [
            'type' => SubscriptionEventType::TRIAL,
            'transaction_id' => time(),
            'original_transaction_id' => time(),
            'price' => 0,
            'price_in_purchased_currency' => 0,
            'expiration_at_ms' => Carbon::now()->addDays($plan->duration)->timestamp * 1000,
        ];

        try {
            $this->createNewSubscription($user, $plan, $trialSubscriptionData);
        } catch (\Throwable $throwable) {
            throw new CreateSubscriptionException($throwable->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Create new subscription for User
     * @param User $user
     * @param Plan $plan
     * @param array $event
     * @return Subscription
     * @throws CreateSubscriptionException
     */
    public function createNewSubscription(User $user, Plan $plan, array $event): Subscription
    {
        try {
            $this->cancelSubscription($user);

            $subscription = Subscription::query()->make([
                'event_type' => Arr::get($event, 'type'),
                'transaction_id' => Arr::get($event, 'transaction_id'),
                'original_transaction_id' => Arr::get($event, 'original_transaction_id'),
                'country_code' => Arr::get($event, 'country_code'),
                'currency' => Arr::get($event, 'currency'),
                'price' => Arr::get($event, 'price'),
                'price_in_purchased_currency' => Arr::get($event, 'price_in_purchased_currency'),
                'store' => Arr::get($event, 'store'),
                'created_at' => Carbon::now(),
                'expired_at' => Carbon::createFromTimestamp(Arr::get($event,'expiration_at_ms') / 1000)
                    ->format('Y-m-d H:i:s'),
                'is_active' => !($plan->duration === 0),
            ]);

            $subscription->user()->associate($user);
            $subscription->plan()->associate($plan);

            $subscription->save();
        } catch (\Throwable $exception) {
            throw new CreateSubscriptionException($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $subscription;
    }

    /**
     * Create subscription by date
     * @param User $user
     * @param Plan $plan
     * @return Subscription
     * @throws CreateSubscriptionException
     */
    public function createSubscriptionByDate(User $user, Plan $plan, mixed $subscriptionExpiredDate): Subscription
    {
        if (Carbon::now()->gte($subscriptionExpiredDate)) {
            throw new CreateSubscriptionException(
                'Дата окончания подписки не должна быть меньше сегодняшней',
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        try {
            $this->cancelSubscription($user);

            $subscription = $this->createNewSubscription($user, $plan, [
                'type' => SubscriptionEventType::ACTIVATE_FROM_ADMIN,
                'transaction_id' => time(),
                'original_transaction_id' => time(),
                'price' => 0,
                'price_in_purchased_currency' => 0,
                'expiration_at_ms' => Carbon::parse($subscriptionExpiredDate)->timestamp * 1000,
            ]);
        } catch (\Throwable $exception) {
            throw new CreateSubscriptionException($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $subscription;
    }

    /**
     * @param User $user
     * @param Plan $plan
     * @param array $event
     * @return Subscription
     * @throws CreateSubscriptionException
     */
    public function continueSubscription(User $user, Plan $plan, array $event): Subscription
    {
        $this->cancelSubscription($user);
        return $this->createNewSubscription($user, $plan, $event);
    }

    /**
     * @throws CreateSubscriptionException
     */
    public function cancelSubscription(User $user): ?Subscription
    {
        try {
            $subscription = $user->activeSubscription();

            if ($subscription) {
                $subscription->is_active = false;
                $subscription->save();
            }
        } catch (\Throwable $exception) {
            throw new CreateSubscriptionException($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $subscription;
    }
}
