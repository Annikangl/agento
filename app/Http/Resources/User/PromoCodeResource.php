<?php

namespace App\Http\Resources\User;

use App\Models\User\Promocode;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Promocode */
class PromoCodeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'discount' => $this->discount,
            'used_count' => $this->used_count,
            'created_at' => $this->created_at->format('d.m.Y'),
        ];
    }
}
