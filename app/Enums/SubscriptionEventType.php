<?php

namespace App\Enums;

enum SubscriptionEventType: string
{
    case TRIAL = 'TRIAL';
    case INITIAL_PURCHASE = 'INITIAL_PURCHASE';
    case ACTIVATE_FROM_ADMIN = 'ACTIVATE_FROM_ADMIN';
    case RENEWAL = 'RENEWAL';
    case CANCELLATION = 'CANCELLATION';
    case EXPIRATION = 'EXPIRATION';

    public function toString(): ?string
    {
        return match ($this) {
            self::TRIAL => 'Пробный',
            self::INITIAL_PURCHASE => 'Первичная покупка',
            self::ACTIVATE_FROM_ADMIN => 'Активация администратором',
            self::RENEWAL => 'Продление',
            self::CANCELLATION => 'Отмена',
            self::EXPIRATION => 'Истечение',
        };
    }
}
