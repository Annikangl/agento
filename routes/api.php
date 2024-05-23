<?php

use App\Http\Controllers\Api\v1\Account\BalanceController;
use App\Http\Controllers\Api\v1\Analytics\UserAnalyticsController;
use App\Http\Controllers\Api\v1\App\ScrapperTaskController;
use App\Http\Controllers\Api\v1\Auth\EmailVerificationController;
use App\Http\Controllers\Api\v1\Auth\LoginController;
use App\Http\Controllers\Api\v1\Auth\LogoutController;
use App\Http\Controllers\Api\v1\Auth\PasswordRecoverController;
use App\Http\Controllers\Api\v1\Auth\RegisterController;
use App\Http\Controllers\Api\v1\Cabinet\CabinetController;
use App\Http\Controllers\Api\v1\Catalog\CatalogController;
use App\Http\Controllers\Api\v1\GetAppVersionController;
use App\Http\Controllers\Api\v1\GetBannerController;
use App\Http\Controllers\Api\v1\Notification\PushNotificationController;
use App\Http\Controllers\Api\v1\Offer\CommercialOfferController;
use App\Http\Controllers\Api\v1\Payment\SubscriptionController;
use App\Http\Controllers\Api\v1\Promocode\PromoCodeController;
use App\Http\Controllers\Api\v1\ReferralController;
use App\Http\Controllers\Api\v1\Selection\AdvertController;
use App\Http\Controllers\Api\v1\Selection\SearchAddressController;
use App\Http\Controllers\Api\v1\Selection\SelectionController;
use App\Http\Controllers\Api\v1\Ticket\TicketController;
use Illuminate\Support\Facades\Route;


Route::group(['prefix' => 'auth', 'as' => 'auth.'], function () {
    Route::post('/register', RegisterController::class)->name('register');
    Route::post('/login', LoginController::class)->name('login');
    Route::post('/logout', LogoutController::class)->name('logout');

    Route::post('/email/send', [EmailVerificationController::class, 'sendVerifyCode']);
    Route::post('/email/exists', [EmailVerificationController::class, 'checkEmailExists']);
    Route::post('/password/recover', PasswordRecoverController::class);
});

Route::group(['prefix' => 'moonshine', 'as' => 'moonshine.',], function () {
    Route::prefix('users')->group(function () {
        Route::post('{user}/subscription/cancel', [SubscriptionController::class, 'cancelSubscription'])
            ->name('user.subscription.cancel');
    });

    Route::prefix('app')->group(function () {
       Route::get('scrappers/{scrapperTask}/log/download', [ScrapperTaskController::class, 'downloadLog'])
           ->name('scrapper.log.download');
    });
});

    Route::prefix('tickets')->group(function () {
        Route::post('/', [TicketController::class, 'store']);
    });

    Route::patch('selection/advert/{advert}/like', [AdvertController::class, 'toggleLike'])
        ->middleware('x-api-key')
        ->name('toggle-like');

    Route::middleware(['auth:sanctum', 'check-subscription'])->group(function () {

        Route::prefix('app')->group(function () {
            Route::get('/version', GetAppVersionController::class);
            Route::get('/banner', GetBannerController::class);

            Route::prefix('analytics')->group(function () {
                Route::post('user/visit', [UserAnalyticsController::class, 'trackVisit']);
            });
        });

        Route::prefix('cabinet')->group(function () {
            Route::get('/', [CabinetController::class, 'index']);
            Route::put('/', [CabinetController::class, 'update']);
            Route::delete('/{user?}', [CabinetController::class, 'delete']);
            Route::put('/password', [CabinetController::class, 'chanePassword']);
        });

        Route::prefix('offers')->group(function () {
            Route::get('/', [CommercialOfferController::class, 'byUser']);
            Route::get('/{commercialOffer}/status', [CommercialOfferController::class, 'checkStatus']);
            Route::get('/{commercialOffer}', [CommercialOfferController::class, 'show']);
            Route::post('/', [CommercialOfferController::class, 'store']);
            Route::delete('/{commercialOffer}', [CommercialOfferController::class, 'delete']);
        });

        Route::prefix('user')->group(function () {
            Route::get('/selection', [SelectionController::class, 'getByUser'])
                ->name('selection.get-by-user');
            Route::get('/balance', [BalanceController::class, 'getBalance'])
                ->name('balance.get-balance');
            Route::post('/withdrawal', [BalanceController::class, 'createWithdrawal'])
                ->name('balance.create-withdrawal');
            Route::get('referrals', [ReferralController::class, 'byUser']);
            Route::get('/notifications/history', [PushNotificationController::class, 'getHistoryByUser'])
                ->name('user.notifications.history');
        });

        Route::group(['prefix' => 'selection', 'as' => 'selection.'], function () {
            Route::post('/', [SelectionController::class, 'store'])->name('store');
            Route::get('/{selection}/catalog', [CatalogController::class, 'getBySelection'])
                ->name('catalog');
            Route::post('/{selection}/link/generate', [SelectionController::class, 'generateLink'])
                ->name('generate-link');
            Route::get('/{selection}/adverts', [AdvertController::class, 'getBySelection'])
                ->name('get-adverts-by-selection');
            Route::delete('{selection}', [SelectionController::class, 'destroy'])
                ->name('delete');

            Route::prefix('advert')->group(function () {
                Route::post('/', [AdvertController::class, 'createAdverts'])->name('create-adverts');
            });
        });

        Route::get('location/search', SearchAddressController::class)->name('address-search');

        Route::prefix('promocode')->group(function () {
            Route::post('activate', [PromoCodeController::class, 'activate'])->name('promocode.activate');
        });

    });

    Route::fallback(function () {
        return response()->json(['status' => false, 'message' => 'Object not found'])->setStatusCode(404);
    });
