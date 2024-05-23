<?php

namespace App\Models\User;

use App\Enums\User\WithdrawalStatus;
use App\Models\Analytics\AnalyticsAppVisit;
use App\Models\Notification\PushNotification;
use App\Models\Offer\CommercialOffer;
use App\Models\Selection\Selection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;
use MoonShine\Models\MoonshineUser;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Class User
 *
 * @property int $id
 * @property int $supervisor_id
 * @property int $referrer_id
 * @property string $name
 * @property string $country
 * @property string $phone
 * @property string $email
 * @property string $password
 * @property string|null $fcm_token
 * @property string $device_name
 * @property string $email_verified_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */

class User extends Authenticatable implements HasMedia
{
    use HasApiTokens, HasFactory, Notifiable, InteractsWithMedia;

    const ALLOWED_UPDATE_DAYS = 7;

    protected $fillable = [
        'supervisor_id',
        'referrer_id',
        'name',
        'country',
        'phone',
        'email',
        'password',
        'fcm_token',
        'device_name',
        'email_verified_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'fcm_token'
    ];

    protected $casts = [
        'password' => 'hashed',
    ];


    /**
     * Check if user has active subscription
     * @return bool
     */
    public function hasActiveSubscription(): bool
    {
        return !is_null($this->activeSubscription());
    }

    public function isAdmin(): bool
    {
        return false;
    }

    /**
     * Check if user can create withdrawal request
     * @param int $withdrawalAmount
     * @return bool
     */
    public function canWithdrawal(int $withdrawalAmount): bool
    {
        return $this->balance->can_withdrawal
            && $this->balance->amount > $withdrawalAmount
            && !$this->hasPendingWithdrawals();
    }

    /**
     * Check if user has pending withdrawals
     * @return bool
     */
    public function hasPendingWithdrawals(): bool
    {
        return $this->lastWithdrawal->status === WithdrawalStatus::PENDING;
    }

    public function commercialOffers(): HasMany
    {
        return $this->hasMany(CommercialOffer::class);
    }


    public function selections(): HasMany
    {
        return $this->hasMany(Selection::class);
    }


    public function promocode(): HasOne
    {
        return $this->hasOne(Promocode::class);
    }

    public function balance(): HasOne
    {
        return $this->hasOne(Balance::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function lastTransaction(): HasOne
    {
        return $this->hasOne(Transaction::class)->latestOfMany();
    }

    public function withdrawals(): HasMany
    {
        return $this->hasMany(Withdrawal::class);
    }

    public function pushNotifications(): HasMany
    {
        return $this->hasMany(PushNotification::class);
    }

    public function lastWithdrawal(): HasOne
    {
        return $this->hasOne(Withdrawal::class)->latestOfMany();
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(MoonshineUser::class, 'supervisor_id');
    }

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function userReferals(): HasMany
    {
        return $this->hasMany(UserReferral::class, 'referrer_id');
    }

    public function lastVisit(): HasOne
    {
        return $this->hasOne(AnalyticsAppVisit::class,'user_id')->latestOfMany();
    }


    /**
     * Update user fcm token
     * @param string|null $fcmToken
     * @return void
     */
    public function updateFcmToken(?string $fcmToken): void
    {
        $this->fcm_token = $fcmToken;
        $this->save();
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription(): ?Subscription
    {
        return $this->subscriptions()->where('is_active', true)->first();
    }

    public function lastSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)->latestOfMany();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatars')
            ->useFallbackPath(public_path('assets/images/no_photo.jpg'))
            ->useFallbackUrl(asset('assets/images/no_photo.jpg'))
            ->registerMediaConversions(function (Media $media) {
                $this
                    ->addMediaConversion('thumb')
                    ->fit(Manipulations::FIT_CROP, 320, 240)
                    ->sharpen(10)
                    ->queued();
            });
    }
}
