<?php

namespace App\Http\Resources\App;

use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Banner */
class BannerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'path' => $this->getFirstMediaUrl('banner', 'thumb_1280'),
        ];
    }
}
