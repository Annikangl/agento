<?php

namespace App\Enums\User;

enum WithdrawalStatus: string
{
    case PENDING = 'pending';
    case REJECTED = 'rejected';

    case COMPLETED = 'completed';
}
