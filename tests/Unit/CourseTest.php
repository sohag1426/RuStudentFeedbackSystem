<?php

namespace Tests\Unit;

use App\Enums\Semester;
use App\Enums\Year;
use App\Models\Course;
use Tests\TestCase;

class CourseTest extends TestCase
{
    public function test_course_year_and_semester_cast_to_enums()
    {
        $course = new Course;
        $course->setRawAttributes([
            'code' => 'CSE101',
            'name' => 'Structured Programming',
            'year' => '1st Year',
            'semester' => '1st Semester',
        ], true);

        $this->assertInstanceOf(Year::class, $course->year);
        $this->assertSame(Year::FIRST, $course->year);
        $this->assertSame('1st Year', $course->year->value);

        $this->assertInstanceOf(Semester::class, $course->semester);
        $this->assertSame(Semester::FIRST, $course->semester);
        $this->assertSame('1st Semester', $course->semester->value);
    }

    public function test_course_year_and_semester_can_be_null()
    {
        $course = new Course;
        $course->setRawAttributes([
            'code' => 'CSE101',
            'name' => 'Structured Programming',
            'year' => null,
            'semester' => null,
        ], true);

        $this->assertNull($course->year);
        $this->assertNull($course->semester);
    }
}
