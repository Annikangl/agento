<?php

namespace App\Http\Resources\User;

use App\Models\User\Subscription;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var Subscription|JsonResource $this */

        $this->load('plan');

        return [
            'id' => $this->id,
            'plan_id' => $this->plan_id,
            'plan' => $this->whenLoaded('plan', $this->plan->name),
            'expired_at' => $this->expired_at
        ];
    }
}
