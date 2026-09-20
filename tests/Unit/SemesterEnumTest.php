<?php

namespace Tests\Unit;

use App\Enums\Semester;
use PHPUnit\Framework\TestCase;

class SemesterEnumTest extends TestCase
{
    public function test_semester_enum_has_expected_cases_and_values()
    {
        $expected = [
            '1st Semester',
            '2nd Semester',
            '3rd Semester',
        ];

        $this->assertSame($expected, Semester::values());
        $this->assertSame('3rd Semester', Semester::THIRD->value);
    }
}
