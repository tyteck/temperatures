<?php

declare(strict_types=1);

namespace App\Enums;

enum PeriodUnits: int
{
    case DAY = 0;

    case WEEK = 1;

    case MONTH = 2;

    case YEAR = 3;

    public function value(): string
    {
        return match ($this) {
            self::DAY => '',
            self::WEEK => '',
            self::MONTH => '=',
            self::YEAR => '',
        };
    }

    public function sqlite()
    {
        return match ($this) {
            self::DAY => '',
            self::WEEK => '',
            self::MONTH => '%Y-%m',
            self::YEAR => '',
        };
    }

    public function mysql()
    {
        return match ($this) {
            self::DAY => '',
            self::WEEK => '',
            self::MONTH => '%Y-%m',
            self::YEAR => '',
        };
    }
}
