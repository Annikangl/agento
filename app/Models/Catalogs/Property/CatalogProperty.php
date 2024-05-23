<?php

namespace App\Models\Catalogs\Property;

use App\Enums\Selection\AddressType;
use App\Enums\Selection\CompletionEnum;
use App\Enums\Selection\SelectionSizeUnit;
use App\Models\Selection\Advert;
use App\Traits\HasCatalogName;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * @property string $dataAdd
 * @property string $dataLastUpdated
 * @property Carbon $listed_date
 */
class CatalogProperty extends Model
{
    use HasFactory, HasCatalogName;

    protected $table = 'catalog_property';

    protected $fillable = [
        'active_flag',
        'update_flag',
        'data_add',
        'data_last_update',
        'miss_try_count',
        'source',
        'listed_date',
        'title',
        'price',
        'period',
        'main_photo',
        'property_type',
        'full_location_path',
        'property_city',
        'property_tower',
        'property_community',
        'property_subcommunity',
        'bedrooms',
        'bathrooms',
        'size_sqft',
        'size_m2',
        'geo_lat',
        'geo_lon',
        'reference',
        'description',
        'deal_type',
        'amenity_names',
        'completion_type',
        'furnished',
        'rera',
        'get_images',
    ];

    protected $casts = [
        'active_flag' => 'boolean',
        'update_flag' => 'boolean',
        'listed_date' => 'datetime',
    ];

    /**
     * Sort by price column
     * @param Builder $builder
     * @param string $direction
     * @return Builder
     */
    public function scopeSortByPrice(Builder $builder, string $direction = 'asc'): Builder
    {
        if (in_array(strtolower($direction), ['asc', 'desc'])) {
            return $builder->orderBy('price', $direction);
        }

        return $builder;
    }

    /**
     * Sort by data_add column
     * @param Builder $builder
     * @param string $direction
     * @return Builder
     */
    public function scopeSortByNewest(Builder $builder, string $direction = 'asc'): Builder
    {
        if (in_array(strtolower($direction), ['asc', 'desc'])) {
            return $builder->orderBy('listed_date', $direction);
        }

        return $builder;
    }

    /**
     * Get only active records
     * @param Builder $builder
     * @return Builder
     */
    public function scopeActive(Builder $builder): Builder
    {
        return $builder->where('active_flag', true);
    }

    /**
     * Get records by price range
     * @param Builder $builder
     * @param int|null $priceFrom
     * @param int|null $priceTo
     * @return Builder
     */
    public function scopeByBudgetRange(Builder $builder, ?int $priceFrom, ?int $priceTo): Builder
    {
        if (!$priceFrom && !$priceTo) {
            return $builder;
        }

        return $builder->where('price', '>=', $priceFrom)
            ->where('price', '<=', $priceTo);
    }

    /**
     * Get records by property type
     * @param Builder $builder
     * @param string $type
     * @return Builder
     */
    public function scopeByPropertyType(Builder $builder, string $type): Builder
    {
        return $builder->where('property_type', $type);
    }

    /**
     * Get records by deal type
     * @param Builder $builder
     * @param string $type
     * @return Builder
     */
    public function scopeByDealType(Builder $builder, string $type): Builder
    {
        return $builder->where('deal_type', $type);
    }

    /**
     * Get records by completion. If completion is any returns builder
     * @param Builder $builder
     * @param string $completion
     * @return Builder
     */
    public function scopeByCompletion(Builder $builder, string $completion): Builder
    {
        if ($completion === CompletionEnum::ANY->value) {
            return $builder;
        }

        return $builder->where('completion_type', $completion);
    }

    /**
     * Get records by bedrooms count
     * @param Builder $builder
     * @param array $bedrooms
     * @return Builder
     */
    public function scopeByBedroomsCount(Builder $builder, array $bedrooms): Builder
    {
        return $builder->whereIn('bedrooms', $bedrooms);
    }

    /**
     * Get records by sqft size
     * @param Builder $builder
     * @param string|null $units
     * @param int|null $from
     * @param int|null $to
     * @return Builder
     */
    public function scopeBySize(Builder $builder, ?string $units, ?int $from, ?int $to): Builder
    {
        if (is_null($from) || is_null($to)) {
            return $builder;
        }

        if ($units === SelectionSizeUnit::SQFT->value) {
            return $builder->whereBetween('size_sqft', [$from, $to]);
        }

        return $builder->whereBetween('size_m2', [$from, $to]);
    }

    /**
     * Scope search by location. If type is not provided search by full location path
     * @param Builder $builder
     * @param string|null $type
     * @param string $address
     * @return Builder
     */
    public function scopeByAddress(Builder $builder, ?string $type, string $address): Builder
    {
        if (!$type) {
            return $this->byFullLocationPath($builder, $address);
        }

        $type = strtoupper($type);

        $addressParts = str($address)->explode(',');

        // If address type is city, search by city. City name is first part of address
        if ($type === AddressType::CITY->value) {
            return $builder->where('property_city', trim($addressParts->first()));
        }

        // If address type is community, search by community. Community name is first part of address, city is second
        if ($type === AddressType::COMMUNITY->value) {
            return $builder->where('property_community', $addressParts->first())
                ->where('property_city', trim($addressParts->get(1)));
        }

        # If address type is subcommunity, search by subcommunity. Subcommunity name is first part of address,
        # community is second, city is third
        if ($type === AddressType::SUBCOMMUNITY->value) {
            return $builder->where('property_subcommunity', $addressParts->first())
                ->where('property_community', trim($addressParts->get(2)))
                ->where('property_city', trim($addressParts->get(1)));
        }

        // If address type is tower, search by tower. Tower name is first part of address, community is second,
        // city is third
        if ($type === AddressType::TOWER->value) {
            return $builder->where('property_tower', $addressParts->first())
                ->where('property_community', trim($addressParts->get(2)))
                ->where('property_city', trim($addressParts->get(1)));
        }

        return $builder;
    }

    /**
     * Search by full location path use LIKE operator
     * @param Builder $builder
     * @param string $address
     * @return Builder
     */
    public function scopeByFullLocationPath(Builder $builder, string $address): Builder
    {
        return $builder->where('full_location_path', 'LIKE', "%$address%");
    }

    public function images(): HasMany
    {
        return $this->hasMany(CatalogPropertyImg::class, 'id');
    }

    public function adverts(): MorphMany
    {
        return $this->morphMany(Advert::class, 'catalogable');
    }

    /**
     * Define cast to array for coordinates
     * @return Attribute
     */
    protected function coords(): Attribute
    {
        return Attribute::make(
            get: fn() => [
                'lat' => $this->geo_lat,
                'lon' => $this->geo_lon,
            ]
        );
    }

    /**
     * Define cast to date string from ms
     * @return Attribute
     */
    protected function dataAdd(): Attribute
    {
        return Attribute::make(
            get: fn($value) => Carbon::parse($value)->format('d.m.Y H:i')
        );
    }

    /**
     * Define cast to date string from ms
     * @return Attribute
     */
    protected function dataLastUpdated(): Attribute
    {
        return Attribute::make(
            get: fn($value) => Carbon::parse($value)->format('d.m.Y H:i')
        );
    }

    /**
     * Define cast size m2
     * @return Attribute
     */
    protected function sizem2(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ? number_format($value, 1, '.', '') : null
        );
    }

    /**
     * Get amenity names as collection
     * @return Collection
     */
    public function getAmenityNamesCollection(): Collection
    {
        return $this->amenity_names ? collect(explode(',', $this->amenity_names)) : collect();
    }

    /**
     * Get active items with images count
     * @return Cache
     */
    public static function getActiveHasImagesCount(): mixed
    {
        return Cache::remember('catalog_property_count', 60 * 60 * 24, function () {
            return self::has('images')->where('active_flag', true)->count();
        });
    }
}
