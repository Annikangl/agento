<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\User\User;
use MoonShine\Models\MoonshineUser;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(MoonshineUser $user)
    {
        return $user->isSuperUser();
    }

    public function view(MoonshineUser $user, User $item)
    {
        return $user->isSuperUser();
    }

    public function create(MoonshineUser $user)
    {
        return $user->isSuperUser();
    }

    public function update(MoonshineUser $user, User $item)
    {
        return $user->isSuperUser();
    }

    public function delete(MoonshineUser $user, User $item)
    {
        return $user->isSuperUser();
    }

    public function restore(MoonshineUser $user, User $item)
    {
        return $user->isSuperUser();
    }

    public function forceDelete(MoonshineUser $user, User $item)
    {
        return $user->isSuperUser();
    }

    public function massDelete(MoonshineUser $user)
    {
        return $user->isSuperUser();
    }
}
