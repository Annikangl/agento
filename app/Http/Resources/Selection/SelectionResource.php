<?php

namespace App\Http\Resources\Selection;

use App\Http\Resources\Selection\Advert\AdvertResource;
use App\Models\Selection\Selection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Selection */

class SelectionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'web_link' => $this->uniqueid ? route('selection.show', $this->uniqueid) : null,
            'title' => $this->title,
            'deal_type' => $this->deal_type,
            'property_type' => $this->property_type,
            'completion' => $this->completion,
            'beds' => $this->beds,
            'size_from' => $this->size_from,
            'size_to' => $this->size_to,
            'size_units' => $this->size_units,
            'location' => $this->location,
            'budget_from' => $this->budget_from,
            'budget_to' => $this->budget_to,
            'is_liked' => $this->is_liked,
            'created_at' => $this->created_at->format('d.m.Y H:i'),
            'adverts' => $this->whenLoaded('adverts',  AdvertResource::collection($this->adverts)),
        ];
    }
}
