<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Banner extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'banner',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function registerMediaCollections():void
    {
        $this->addMediaCollection('banner')->singleFile();
    }

    /**
     * @throws InvalidManipulation
     */
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb_1280')
            ->performOnCollections('banner')
            ->fit(Manipulations::FIT_CROP, 1280, 720)
            ->sharpen(10)
            ->queued();
    }
}
