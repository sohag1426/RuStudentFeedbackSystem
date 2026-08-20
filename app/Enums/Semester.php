<?php

namespace App\Enums;

enum Semester: string
{
    case FIRST = '1st Semester';
    case SECOND = '2nd Semester';

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
