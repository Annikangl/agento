<?php

declare(strict_types=1);

namespace App\Models\User;


class MoonshineUser extends \MoonShine\Models\MoonshineUser
{
    protected $fillable = [
        'email',
        'moonshine_user_role_id',
        'password',
        'name',
        'avatar',
        'phone',
        'balance',
    ];

    protected $casts = [
        'balance' => 'integer'
    ];

    protected $hidden = [
        'password',
    ];
}
