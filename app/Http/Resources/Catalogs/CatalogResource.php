<?php

namespace App\Http\Resources\Catalogs;

use App\Models\Catalogs\Property\CatalogProperty;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin CatalogProperty */
class CatalogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'active_flag' => $this->active_flag,
            'property_type' => $this->property_type,
            'catalog_name' => $this->catalog_name,
            'title' => $this->title,
            'source' => $this->source,
            'price' => $this->price,
            'coords' => $this->coords,
            'main_photo' => $this->main_photo,
            'property_city' => $this->property_city,
            'property_community' => $this->property_community,
            'property_subcommunity' => $this->property_subcommunity,
            'property_tower' => $this->property_tower,
            'size_m2' => $this->size_m2,
            'size_sqft' => $this->size_sqft,
            'created_at' => $this->listed_date->format('d.m.Y H:i'),
            'images' => $this->whenLoaded('images', CatalogImagesResource::collection($this->images)),
        ];
    }
}
