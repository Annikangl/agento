<?php

namespace App\Http\Resources\User\Account;

use App\Http\Resources\User\UserResource;
use App\Models\User\Balance;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Balance */
class UserBalanceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'amount' => $this->amount,
        ];
    }
}
