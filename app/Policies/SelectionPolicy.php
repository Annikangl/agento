<?php

namespace App\Policies;

use App\Models\Selection\Selection;
use App\Models\User\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SelectionPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Selection $selection): bool
    {
        return $user->id === $selection->user_id;
    }

    public function delete(User $user, Selection $selection): bool
    {
        return $user->id === $selection->user_id;
    }
}
