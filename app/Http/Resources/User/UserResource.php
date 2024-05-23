<?php

namespace App\Http\Resources\User;

use App\Models\User\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin User  */
class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'country' => $this->country,
            'email' => $this->email,
            'phone' => $this->phone,
            'created_at' => $this->created_at,
            'promocode' => $this->whenLoaded('promocode', new PromoCodeResource($this->promocode))
        ];
    }
}
