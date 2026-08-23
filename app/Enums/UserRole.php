<?php

namespace App\Enums;

enum UserRole: string
{
    case TEACHER = 'teacher';
    case DEPARTMENT_CHAIR = 'DepartmentChair';
    case DEPARTMENT_MANAGER = 'DepartmentManager';
    case ADMIN = 'admin';
    case SUPER_ADMIN = 'SuperAdmin';

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
