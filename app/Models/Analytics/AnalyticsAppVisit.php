<?php

namespace App\Models\Analytics;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class AnalyticsAppVisit extends Model
{
    use HasFactory;

    protected $table = 'analytics_app_visits';

    protected $fillable = [
        'user_id',
        'device_id',
        'device_name',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function getAllByDay(): mixed
    {
        return Cache::remember(self::class . 'all_users_day_app_visits', 60 * 24, function () {
            return self::query()
                ->where('created_at', '>=', Carbon::now()->subDay())
                ->select('device_id')
                ->count('device_id');
        });
    }

    public static function getUniqueAppVisitsByDay(): mixed
    {
        return Cache::remember(self::class . 'unique_users_day_app_visits', 60 * 24, function () {
            return self::query()
                ->where('created_at', '>=', Carbon::now()->subDay())
                ->distinct('device_id')
                ->count('device_id');
        });
    }

    public static function getUniqueAppVisitsByWeek(): mixed
    {
        return Cache::remember(self::class . 'unique_users_week_app_visits', 60 * 24 * 7, function () {
            return self::query()
                ->selectRaw('COUNT(DISTINCT user_id) as user_count, DATE(created_at) as created_date')
                ->where('created_at', '>=', Carbon::now()->subDays(7))
                ->groupBy('created_date')
                ->get();
        });
    }

    public static function getUniqueAppVisitsByDays(int $days = 7): mixed
    {
        return Cache::remember(self::class . 'unique_users_week_app_visits', 60 * 24 * 7, function () use ($days) {
            return self::query()
                ->selectRaw('COUNT(DISTINCT user_id) as user_count, DATE(created_at) as created_date')
                ->where('created_at', '>=', Carbon::now()->subDays($days))
                ->groupBy('created_date')
                ->get();
        });
    }
}
