<?php

namespace App\Models\Catalogs;

use App\Enums\Selection\AddressType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'children',
        'lat',
        'lon',
        'path',
        'count_ads',
    ];

    public $timestamps = false;

    protected $casts = [
        'type' => AddressType::class,
    ];

    /**
     * Search locatuions by name or path and ordering by type
     * @param Builder $builder
     * @param string $searchText
     * @return Builder
     */
    public function scopeBySearch(Builder $builder, string $searchText): Builder
    {
        $cities = self::query()->where('type', AddressType::CITY)
            ->where('name', 'like', "%$searchText%")->limit(5);

        $communities = self::query()->where('type', AddressType::COMMUNITY)
            ->where('name', 'like', "%$searchText%")->limit(5);

        $subcommunities = self::query()->where('type', AddressType::SUBCOMMUNITY)
            ->where('name', 'like', "%$searchText%")->limit(10);

        $towers = self::query()->where('type', AddressType::TOWER)
            ->where('name', 'like', "%$searchText%")->limit(10);

        return $cities->union($communities, true)->union($subcommunities, true)->union($towers, true);
    }
}
