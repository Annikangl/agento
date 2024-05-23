<?php

namespace App\MoonShine\Resources\User;

use App\Models\Notification\PushNotification;
use App\Models\User\Plan;
use App\Models\User\User;
use App\MoonShine\Resources\CommercialOfferResource;
use App\MoonShine\Resources\PushNotificationResource;
use App\MoonShine\Resources\Selections\SelectionResource;
use App\UseCases\Notifications\PushNotificationService;
use App\UseCases\SubscriptionService;
use App\UseCases\User\UserService;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use MoonShine\ActionButtons\ActionButton;
use MoonShine\Components\FormBuilder;
use MoonShine\Fields\Date;
use MoonShine\Fields\Field;
use MoonShine\Fields\Fields;
use MoonShine\Fields\Hidden;
use MoonShine\Fields\ID;
use MoonShine\Fields\Phone;
use MoonShine\Fields\Preview;
use MoonShine\Fields\Relationships\HasMany;
use MoonShine\Fields\Relationships\HasOne;
use MoonShine\Fields\Text;
use MoonShine\MoonShineRequest;
use MoonShine\MoonShineUI;
use MoonShine\Pages\Page;
use MoonShine\Resources\ModelResource;
use MoonShine\TypeCasts\ModelCast;

class UserResource extends ModelResource
{
    protected string $model = User::class;

    protected string $title = 'Пользователи';

    protected string $column = 'name';

    protected bool $withPolicy = true;

    protected array $with = ['commercialOffers', 'selections', 'balance', 'promocode'];

    private UserService $service;
    private SubscriptionService $subscriptionService;
    private PushNotificationService $pushNotificationService;

    public function __construct()
    {
        $this->service = app(UserService::class);
        $this->pushNotificationService = app(PushNotificationService::class);
        $this->subscriptionService = app(SubscriptionService::class);
    }

    public function indexFields(): array
    {
        return [
            ID::make()->sortable()->showOnExport(),
            Text::make('Имя', 'name')->showOnExport(),
            Phone::make('Email', 'email')->showOnExport(),
            Phone::make('Телефон', 'phone')->showOnExport()->nullable()->hideOnIndex(),
            Preview::make(
                label: 'Доступ к приложению',
                column: 'subscription',
                formatted: fn(User $user) => $user->hasActiveSubscription() ? 'Открыт' : 'Закрыт'
            )
                ->badge(fn($s, Field $field) => $field->getData()->hasActiveSubscription() ? 'green' : 'red'),
            HasMany::make(
                'Созданные PDF',
                'commercialOffers',
                resource: new CommercialOfferResource()
            )
                ->onlyLink()
                ->hideOnDetail(),
            HasMany::make(
                'Созданные подборки',
                'selections',
                resource: new SelectionResource()
            )
                ->onlyLink()
                ->hideOnDetail(),
            Preview::make(
                'Баланс',
                'balance',
                formatted: fn(User $user) => $user->balance->amount . '&nbsp;$'
            )
                ->hideOnDetail(),
            Date::make('Дата регистрации', 'created_at'),
        ];
    }

    public function detailFields(): array
    {
        return array_merge($this->indexFields(), [
            HasOne::make('Баланс', 'balance', resource: new BalanceResource()),
            HasOne::make('Промокод', 'promocode', resource: new PromocodeResource()),
            HasMany::make(
                'Созданные PDF',
                'commercialOffers',
                resource: new CommercialOfferResource()
            ),
            HasMany::make(
                'Созданные подборки',
                'selections',
                resource: new SelectionResource()
            ),
            HasMany::make(
                'Push уведомления',
                'pushNotifications',
                resource: new PushNotificationResource()
            ),
        ]);
    }

    public function rules(Model $item): array
    {
        return [];
    }

    public function search(): array
    {
        return ['email', 'phone', 'name'];
    }

    public function formPage(): ?Page
    {
        return $this->getPages()->detailPage();
    }

    public function filters(): array
    {
        return [
            Text::make('Имя', 'name'),
            Text::make('Email', 'email'),
            Text::make('Телефон', 'phone'),
        ];
    }

    public function delete(User|Model $item, ?Fields $fields = null): bool
    {
        try {
            $this->service->delete($item);
        } catch (\Throwable $exception) {
            throw new \DomainException($exception->getMessage());
        }

        return true;
    }

    public function actions(): array
    {
        return [];
    }

    public function detailButtons(): array
    {
        return [
            ActionButton::make(
                label: __('moonshine::ui.resource.actions.close_access'),
                url: route('moonshine.user.subscription.cancel', parameters: ['user' => $this->getItem()])
            )
                ->canSee(fn(User $user) => $user->hasActiveSubscription())
                ->error()
                ->withConfirm(
                    'Снять подписку пользователя?',
                    'Данное действие отменит подписку у текущего пользователя и он не сможет получить доступ к контенту приложения',
                    'Закрыть доступ',
                ),

            ActionButton::make(__('moonshine::ui.resource.actions.give_access'))
                ->success()
                ->canSee(fn(User $user) => !$user->hasActiveSubscription())
                ->inModal(
                    title: "Предоставить доступ к приложению",
                    content: fn() => FormBuilder::make()
                        ->asyncMethod('activateSubscription')
                        ->fields([
                            Date::make(__('moonshine::ui.resource.fields.date_expired'), 'expired_at')->required(),
                            Hidden::make('user_id')->setValue($this->getItem()->id)
                        ])
                        ->submit(__('moonshine::ui.resource.actions.give_access'), ['class' => 'btn-primary'])
                ),

            ActionButton::make(__('moonshine::ui.resource.actions.send_push_notification'))
                ->secondary()
                ->inModal(
                    title: fn() => __('moonshine::ui.resource.actions.send_personal_push_notification'),
                    content: fn() => FormBuilder::make()
                        ->asyncMethod('sendPush')
                        ->fields([
                            Text::make('Заголовок', 'title')
                                ->required()
                                ->placeholder('Введите заголовок сообщения'),
                            Text::make('Сообщение', 'message')
                                ->required()
                                ->placeholder('Введите текст сообщения, не более 255 символов'),
                            Hidden::make('user_id')->setValue($this->getItem()->id)
                        ])
                        ->cast(ModelCast::make(User::class))
                        ->submit(__('moonshine::ui.resource.actions.send_push_notification'), ['class' => 'btn-primary'])
                ),
        ];
    }

    public function getActiveActions(): array
    {
        return ['view', 'delete', 'massDelete'];
    }

    /**
     * Activate subscription for user
     * @param MoonShineRequest $request
     * @return RedirectResponse
     * @throws Exception
     */
    public function activateSubscription(MoonShineRequest $request): RedirectResponse
    {
        $user = User::find($request->input('user_id'));

        $subscriptionPlan = Plan::getFromAdminPlan();

        try {
            $this->subscriptionService->createSubscriptionByDate(
                $user,
                $subscriptionPlan,
                $request->input('expired_at')
            );

            MoonShineUI::toast('Доступ предоставлен!');
        } catch (\Throwable $exception) {
            throw new Exception($exception->getMessage());
        }

        return back();
    }

    /**
     * Send push notificaiton to users
     * @param MoonShineRequest $request
     * @return void
     */
    public function sendPush(MoonShineRequest $request): void
    {
        PushNotification::query()->create([
            'user_id' => $request->input('user_id'),
            'title' => $request->input('title'),
            'content' => $request->input('message')
        ]);

        $this->pushNotificationService->sendToUser(
            userIds: [$request->input('user_id')],
            title: $request->input('title'),
            message: $request->input('message')
        );

        MoonShineUI::toast(__('moonshine::ui.resource.actions.push_notification_sent'));
    }
}
