<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Plan extends Model
{
    use HasFactory;

    const FROM_ADMIN = 'agento_from_admin_plan';
    const FREE_PLAN = 'agento_free_plan';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'price',
        'duration',
        'description',
    ];

    /**
     * Return trial free plan for new users
     * @return self
     */
    public static function getFreePlan(): self
    {
        return Cache::remember('free_plan', now()->addDay(), function () {
            return self::query()->where('name', self::FREE_PLAN)->first();
        });
    }

    /**
     * Return admin plan
     * @return self
     */
    public static function getFromAdminPlan(): self
    {
        return Cache::remember('admin_plan', now()->addDay(), function () {
            return self::query()->where('name', self::FROM_ADMIN)->first();
        });
    }
}
