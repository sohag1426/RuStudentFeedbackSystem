<?php

namespace App\Enums;

enum Year: string
{
    case FIRST = '1st Year';
    case SECOND = '2nd Year';
    case THIRD = '3rd Year';
    case FOURTH = '4th Year';

    /**
     * Get all enum values as an array.
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
