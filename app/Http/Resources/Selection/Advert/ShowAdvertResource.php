<?php

namespace App\Http\Resources\Selection\Advert;

use App\Http\Resources\Catalogs\CatalogImagesResource;
use App\Models\Selection\Advert;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Advert */

class ShowAdvertResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'active_flag' => $this->catalogable->active_flag,
            'property_type' => $this->catalogable->property_type,
            'catalog_name' => $this->catalogable->catalog_name,
            'title' => $this->catalogable->title,
            'source' => $this->catalogable->source,
            'price' => $this->catalogable->price,
            'coords' => $this->catalogable->coords,
            'main_photo' => $this->catalogable->main_photo,
            'property_city' => $this->catalogable->property_city,
            'property_tower' => $this->catalogable->property_tower,
            'property_community' => $this->catalogable->property_community,
            'property_subcommunity' => $this->catalogable->property_subcommunity,
            'size_m2' => $this->catalogable->size_m2,
            'size_sqft' => $this->catalogable->size_sqft,
            'created_at' => $this->catalogable->dataLastUpdated,
            'is_liked' => $this->is_liked,
            'liked_at' => $this->liked_at,
            'images' => $this->whenLoaded(
                'catalogable',
                CatalogImagesResource::collection($this->catalogable->images)
            ),
        ];
    }
}
