<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\User\MoonshineUser;

/** @mixin MoonshineUser */
class SupervisorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var MoonshineUser|JsonResource $this */

        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
        ];
    }
}
