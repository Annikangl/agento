<?php

namespace App\Enums;

enum CommercialOfferStatus: string
{
    case PENDING = 'pending';
    case COMPLETED = 'completed';
    case ERROR = 'error';

    public function toString(): ?string
    {
        return match ($this) {
            self::PENDING => 'Формируется...',
            self::COMPLETED => 'Сформирован',
            self::ERROR => 'Ошибка',
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::PENDING => 'secondary',
            self::COMPLETED => 'green',
            self::ERROR => 'red',
        };
    }
}
