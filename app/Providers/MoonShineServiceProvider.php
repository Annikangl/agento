<?php

namespace App\Providers;

use App\MoonShine\Resources\BannerResource;
use App\MoonShine\Resources\CommercialOfferResource;
use App\MoonShine\Resources\MoonShineUserResource;
use App\MoonShine\Resources\MoonShineUserRoleResource;
use App\MoonShine\Resources\PlanResource;
use App\MoonShine\Resources\Promocodes\SupervisorPromocodeResource;
use App\MoonShine\Resources\PushNotificationResource;
use App\MoonShine\Resources\Selections\SelectionResource;
use App\MoonShine\Resources\SubscriptionResource;
use App\MoonShine\Resources\System\ScrapperTaskResource;
use App\MoonShine\Resources\TicketResource;
use App\MoonShine\Resources\User\UserResource;
use Closure;
use Illuminate\Http\Request;
use MoonShine\Menu\MenuGroup;
use MoonShine\Menu\MenuItem;
use MoonShine\Providers\MoonShineApplicationServiceProvider;

class MoonShineServiceProvider extends MoonShineApplicationServiceProvider
{

    protected function menu(): Closure|array
    {
        return [
            MenuItem::make('moonshine::ui.resource.main', '/admin')
                ->icon('heroicons.chart-bar')
                ->translatable()
                ->canSee(function (Request $request) {
                    return $request->user('moonshine')->isSuperUser();
                }),

            MenuItem::make('moonshine::ui.resource.users', new UserResource())
                ->icon('heroicons.users')
                ->translatable()
                ->canSee(function (Request $request) {
                    return $request->user('moonshine')->isSuperUser();
                }),

            MenuGroup::make('Подписки', [
                MenuItem::make('moonshine::ui.resource.plans', new PlanResource())
                    ->icon('heroicons.gift')->translatable(),
                MenuItem::make('moonshine::ui.resource.purchase_history', new SubscriptionResource())
                    ->icon('heroicons.clock')->translatable()
            ])
                ->icon('heroicons.currency-dollar')
                ->canSee(function (Request $request) {
                    return $request->user('moonshine')->isSuperUser();
                }),

            MenuGroup::make('moonshine::ui.resource.entyties', [
                MenuItem::make('moonshine::ui.resource.creatted_pdfs', new CommercialOfferResource())
                    ->icon('heroicons.document-text')
                    ->translatable()
                    ->canSee(function (Request $request) {
                        return $request->user('moonshine')->isSuperUser();
                    }),
                MenuItem::make('moonshine::ui.resource.selecions', new SelectionResource())
                    ->icon('heroicons.document-text')
                    ->translatable()
                    ->canSee(function (Request $request) {
                        return $request->user('moonshine')->isSuperUser();
                    }),
            ])
                ->translatable()
                ->icon('heroicons.folder')
                ->canSee(function (Request $request) {
                    return $request->user('moonshine')->isSuperUser();
                }),

            MenuItem::make('moonshine::ui.resource.banners', new BannerResource())
                ->translatable()
                ->icon('heroicons.outline.newspaper'),

            MenuItem::make('moonshine::ui.resource.tickets', new TicketResource())
                ->icon('heroicons.chat-bubble-left-ellipsis')
                ->translatable()
                ->canSee(function (Request $request) {
                    return $request->user('moonshine')->isSuperUser();
                }),

            MenuGroup::make('moonshine::ui.resource.notifications.title', [
                MenuItem::make('moonshine::ui.resource.notifications.push', new PushNotificationResource())
                    ->icon('heroicons.bell')
                    ->translatable(),
            ])
                ->icon('heroicons.paper-airplane')
                ->translatable()
                ->canSee(function (Request $request) {
                    return $request->user('moonshine')->isSuperUser();
                }),

//            MenuItem::make('moonshine::ui.resource.promocodes', new PromocodeResource())
//                ->translatable()
//                ->icon('heroicons.tag')
//                ->canSee(function (Request $request) {
//                    return $request->user('moonshine')->isSuperUser();
//                }),

            MenuItem::make('moonshine::ui.resource.my-promocodes', new SupervisorPromocodeResource())
                ->translatable()
                ->icon('heroicons.tag')
                ->canSee(function (Request $request) {
                    return !$request->user('moonshine')->isSuperUser();
                }),

            MenuGroup::make('moonshine::ui.resource.system', [
                MenuItem::make('moonshine::ui.resource.admins_title', new MoonShineUserResource())
                    ->translatable()
                    ->icon('heroicons.users'),
                MenuItem::make('moonshine::ui.resource.role_title', new MoonShineUserRoleResource())
                    ->translatable()
                    ->icon('heroicons.outline.bookmark'),
                MenuItem::make('moonshine::ui.resource.scrappers', new ScrapperTaskResource())
                    ->translatable()
                    ->icon('heroicons.cog'),
                MenuItem::make('Horizon', '/horizon', blank: true)
                    ->translatable()
                    ->icon('heroicons.bug-ant'),
                MenuItem::make('Telescope', '/telescope', blank: true)
                    ->translatable()
                    ->icon('heroicons.bug-ant'),
            ])
                ->translatable()
                ->icon('heroicons.wrench')
                ->canSee(function (Request $request) {
                    return $request->user('moonshine')->isSuperUser();
                }),
        ];
    }

    public function boot(): void
    {
        parent::boot();

        moonShineAssets()->add([
            'assets/css/moonshine/style.css',
        ]);
    }

}
