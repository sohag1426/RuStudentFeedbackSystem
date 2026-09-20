<?php

namespace Tests\Unit;

use App\Enums\Year;
use PHPUnit\Framework\TestCase;

class YearEnumTest extends TestCase
{
    public function test_year_enum_has_expected_cases_and_values()
    {
        $expected = [
            '1st Year',
            '2nd Year',
            '3rd Year',
            '4th Year',
            'Masters',
        ];

        $this->assertSame($expected, Year::values());
        $this->assertSame('Masters', Year::MASTERS->value);
    }
}
