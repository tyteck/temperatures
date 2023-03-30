<?php

declare(strict_types=1);

namespace App\Enums;

enum QueryOperators: string
{
    case EQUAL = 'eq';

    case GREATER_THAN = 'gt';

    case GREATER_THAN_OR_EQUAL = 'gte';

    case LESS_THAN = 'lt';

    case LESS_THAN_OR_EQUAL = 'lte';

    public function value(): string
    {
        return match ($this) {
            self::EQUAL => '=',
            self::GREATER_THAN => '>',
            self::GREATER_THAN_OR_EQUAL => '>=',
            self::LESS_THAN => '<',
            self::LESS_THAN_OR_EQUAL => '<=',
        };
    }
}
