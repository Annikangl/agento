<?php

namespace App\Http\Resources\User;

use App\Models\User\UserReferral;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin UserReferral */
class ReferralResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'referral_id' => $this->referral_id,
            'referal_email' => $this->referral->email,
            'referal_regisered_at' => $this->referral->created_at->format('d.m.Y'),
            'level' => $this->level
        ];
    }
}
