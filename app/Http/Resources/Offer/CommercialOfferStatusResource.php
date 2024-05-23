<?php

namespace App\Http\Resources\Offer;

use App\Models\Offer\CommercialOffer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin CommercialOffer */
class CommercialOfferStatusResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
        ];
    }
}
