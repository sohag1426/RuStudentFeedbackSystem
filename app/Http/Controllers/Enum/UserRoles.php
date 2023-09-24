<?php

namespace App\Http\Controllers\Enum;

enum UserRoles
{
    case site_admin;
    case department_admin;
    case teacher;
}
