<?php

namespace App\Http\Resources\Offer;

use App\Models\Offer\CommercialOffer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin CommercialOffer */
class CommercialOfferResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'source_link' => $this->source_link,
            'source_name' => $this->source_name,
            'title' => $this->title,
            'status' => $this->status,
            'lang' => $this->lang,
            'pdf_path' => $this->pdf_path,
            'created_at' => $this->created_at->format('d.m.y H:i'),
        ];
    }
}
