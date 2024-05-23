<?php

namespace App\Policies;

use App\Models\Offer\CommercialOffer;
use App\Models\User\User;

class CommercialOfferPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function delete(User $user, CommercialOffer $commercialOffer): bool
    {
        return $user->id === $commercialOffer->user_id || $user->isAdmin();
    }
}
