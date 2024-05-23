<?php

namespace App\UseCases;

use App\Exceptions\Api\Promocode\PromocodeException;
use App\Models\User\User;
use App\Models\User\UserReferral;

class ReferralService
{
    /**
     * Max referral level
     * @var int
     */
    protected int $maxLevel = 3;

    /**
     * @throws PromocodeException
     */
    public function addReferral(User $referrer, User $referral, int $level = 1): void
    {
        if (UserReferral::where('referral_id', $referral->id)->doesntExist()) {
            $referral->referrer()->associate($referrer);
            $referral->save();
        }

        if (UserReferral::where('referral_id', $referral->id)->where('level', $level)->exists()) {
            throw new PromocodeException('This user is already referred in the system on this level.');
        }

        UserReferral::create([
            'referrer_id' => $referrer->id,
            'referral_id' => $referral->id,
            'level' => $level
        ]);

        if ($referrer->referrer && $level < $this->maxLevel) {
            $this->addReferral($referrer->referrer, $referral, $level + 1);
        }
    }
}
