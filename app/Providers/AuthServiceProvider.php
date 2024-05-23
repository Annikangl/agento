<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\Selection\Advert;
use App\Models\Selection\Selection;
use App\Policies\AdvertPolicy;
use App\Policies\SelectionPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
        Selection::class => SelectionPolicy::class,
        Advert::class => AdvertPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
