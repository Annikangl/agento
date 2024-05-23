<?php

namespace App\Models\User;

use App\Enums\SubscriptionEventType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Subscription extends Model
{
    use HasFactory;

    const EVENT_TYPE_INITIAL_PURCHASE = 'INITIAL_PURCHASE';
    const EVENT_TYPE_INITIAL_RENEWAL = 'RENEWAL';
    const EVENT_TYPE_INITIAL_TRIAL = 'TRIAL';
    const EVENT_TYPE_CANCELLATION = 'CANCELLATION';
    const EVENT_TYPE_EXPIRATION = 'EXPIRATION';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'plan_id',
        'transaction_id',
        'original_transaction_id',
        'event_type',
        'country_code',
        'currency',
        'price',
        'price_in_purchased_currency',
        'store',
        'cancel_reason',
        'created_at',
        'expired_at',
        'is_active',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'expired_at' => 'datetime',
        'is_active' => 'boolean',
        'event_type' => SubscriptionEventType::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return  $this->belongsTo(Plan::class);
    }

    protected function price(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value / 100.0,
            set: fn ($value) => intval($value * 100),
        );
    }

    protected function expiredAt(): Attribute
    {
        return Attribute::make(
            get: fn($value) => Carbon::parse($value)->format('d-m-Y H:i'),
            set: fn($value) => Carbon::parse($value)->format('Y-m-d H:i:s')
        );
    }

    public static function getEventsTypes(): array
    {
        return [
            self::EVENT_TYPE_INITIAL_PURCHASE => 'Новая',
            self::EVENT_TYPE_INITIAL_RENEWAL => 'Продленная',
            self::EVENT_TYPE_INITIAL_TRIAL => 'Пробный доступ',
        ];
    }
}
