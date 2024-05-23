<?php

namespace App\Models\User;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Promocode extends Model
{
    use HasFactory;

    const BASE_DISCOUNT_PERCENT = 30;

    protected $fillable = [
        'supervisor_id',
        'user_id',
        'code',
        'discount',
        'usage_limit',
        'used_count',
        'expired_at',
    ];

    protected $casts = [
        'usage_limit' => 'integer',
        'used_count' => 'integer',
        'expired_at' => 'datetime:d.m.Y',
    ];

    /**
     * Generate unique code.
     * @return string
     */
    public static function generateUniquePromoCode(): string
    {
        do {
            $code = Str::upper(Str::random(8));
        } while (self::where('code', $code)->exists());

        return $code;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(MoonshineUser::class, 'supervisor_id')
            ->where('moonshine_user_role_id', '<>', 1);
    }
}
