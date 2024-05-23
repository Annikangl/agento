<?php

namespace App\Console\Commands;

use App\Exceptions\Api\Subscriptions\CreateSubscriptionException;
use App\Models\User\User;
use App\UseCases\Notifications\PushNotificationService;
use App\UseCases\SubscriptionService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DisableExpiredSubscriptionCommand extends Command
{
    protected $signature = 'disable-expired-subscription';

    protected $description = 'Disable expired user subscription';

    public function __construct(
        private readonly SubscriptionService $service,
        private readonly PushNotificationService $pushService
    )
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     * @throws CreateSubscriptionException
     */
    public function handle(): void
    {
        $users = User::query()->select(['id'])->with('subscriptions')->get();

        foreach ($users as $user) {
            if ($activeSubscription = $user->activeSubscription()) {
                $today = Carbon::now();
                $expiredAt = Carbon::parse($activeSubscription->expired_at);

                if (Carbon::parse($expiredAt)->lt($today)) {
                    $this->service->cancelSubscription($user);
                }

                if ($expiredAt->diffInDays($today) <= 1) {
                    $this->pushService->sendToUser(
                        userIds: [$user->id],
                        title: "Ваша подписка скоро закончится",
                        message: "Уважаемый пользователь, срок вашей подписки истекает."
                    );
                }
            }

        }
    }
}
