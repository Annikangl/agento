<?php

namespace App\Http\Resources\Selection\Advert;

use App\Models\Selection\Advert;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Advert */

class AdvertResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'is_liked' => $this->is_liked,
            'liked_at' => $this->liked_at,
        ];
    }
}
