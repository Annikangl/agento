<?php

namespace App\Models\Selection;

use App\Models\Catalogs\Property\CatalogProperty;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $selection_id
 * @property int $catalogable_id
 * @property string $catalogable_type
 * @property CatalogProperty $catalogable
 */
class Advert extends Model
{
    use HasFactory;

    protected $fillable = [
        'selection_id',
        'catalogable_id',
        'catalogable_type',
        'is_liked',
        'liked_at',
    ];

    protected $casts = [
        'is_liked' => 'boolean',
    ];

    public function catalogable(): MorphTo
    {
        return $this->morphTo();
    }

    public function selection(): BelongsTo
    {
        return $this->belongsTo(Selection::class);
    }
}
