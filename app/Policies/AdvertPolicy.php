<?php

namespace App\Policies;

use App\Models\Selection\Advert;
use App\Models\Selection\Selection;
use App\Models\User\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdvertPolicy
{
    use HandlesAuthorization;


    public function view(User $user, Advert $advert): bool
    {
        return $user->id === $advert->selection->user_id;
    }

    public function create(User $user, Selection $selection): bool
    {
        return $user->id === $selection->user_id;
    }
}
