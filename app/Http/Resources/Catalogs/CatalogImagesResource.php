<?php

namespace App\Http\Resources\Catalogs;

use App\Models\Catalogs\Property\CatalogProperty;
use App\Models\Catalogs\Property\CatalogPropertyImg;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin CatalogPropertyImg */
class CatalogImagesResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'path' => $this->path,
        ];
    }
}
