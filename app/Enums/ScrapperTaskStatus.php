<?php

namespace App\Enums;

enum ScrapperTaskStatus: int
{
    case STOPPED = 0;
    case RUNNING = 1;

    public function toString(): ?string
    {
        return match($this) {
            self::STOPPED => 'Работа завершена',
            self::RUNNING => 'В процессе',
        };
    }

    public function getColor(): ?string
    {
        return match($this) {
            self::STOPPED => 'gray',
            self::RUNNING => 'info',
        };
    }
}
