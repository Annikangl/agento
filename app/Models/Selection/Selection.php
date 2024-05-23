<?php

namespace App\Models\Selection;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Selection extends Model
{
    use HasFactory;

    const EXPIRED_DAYS = 7;

    protected $fillable = [
        'uuid',
        'user_id',
        'title',
        'deal_type',
        'property_type',
        'completion',
        'beds',
        'size_from',
        'size_to',
        'size_units',
        'location',
        'location_type',
        'budget_from',
        'budget_to',
        'web_link',
        'is_liked',
        'expired_at',
    ];

    protected $casts = [
        'beds' => 'array',
        'is_liked' => 'boolean',
        'expired_at' => 'datetime',
    ];

    /**
     * Get selections by user id
     * @param Builder $builder
     * @param int $userId
     * @return Builder
     */
    public function scopeByUser(Builder $builder, int $userId): Builder
    {
        return $builder->where('user_id', $userId);
    }

    /**
     * Search selections by title
     * @param Builder $builder
     * @param string|null $search
     * @return Builder
     */
    public function scopeBySearch(Builder $builder, ?string $search): Builder
    {
        if (!$search) {
            return $builder;
        }

        return $builder->where('title', 'like', "%$search%");
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function adverts(): HasMany
    {
        return $this->hasMany(Advert::class);
    }

    /**
     * @return int
     */
    public static function getAvgAdvers(): int
    {
        $selectionsCount = self::query()->count();
        $advertsCount = Advert::query()->count();

        if ($selectionsCount === 0 || $advertsCount === 0) {
            return 0;
        }

        return ceil($selectionsCount / $advertsCount);
    }

    /**
     * Get created selections by days. Default 30 days
     * @param int $days
     * @return Builder
     */
    public static function getByDays(int $days = 30): Builder
    {
        return self::query()
            ->selectRaw('COUNT(*) as selection_count, DATE(created_at) as created_date')
            ->where('created_at', '>=', Carbon::now()->subDays($days))
            ->groupBy('created_date');
    }

    /**
     * @return int
     */
    public static function getAvgByUser(): int
    {
        $totalUsersWithSelections = Selection::query()
            ->distinct('user_id')
            ->count('user_id');

        return $totalUsersWithSelections > 0 ? ceil(self::count() / $totalUsersWithSelections) : 0;
    }
}
