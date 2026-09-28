<?php

namespace Tests\Feature\Teacher;

use App\Enums\Semester;
use App\Enums\Year;
use App\Models\Course;
use App\Models\Department;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $department;

    protected $teacher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->department = Department::create([
            'en_name' => 'Computer Science & Engineering',
        ]);

        $this->teacher = User::factory()->create([
            'department_id' => $this->department->id,
            'role' => 'teacher',
        ]);
    }

    public function test_teacher_can_view_courses()
    {
        Course::create([
            'user_id' => $this->teacher->id,
            'department_id' => $this->department->id,
            'code' => 'CSE101',
            'name' => 'Intro to Programming',
            'year' => '1st Year',
            'semester' => '1st Semester',
        ]);

        $response = $this->actingAs($this->teacher)->get(route('courses.index'));
        $response->assertStatus(200);
        $response->assertSee('CSE101');
        $response->assertSee('1st Year');
        $response->assertSee('1st Semester');
    }

    public function test_teacher_can_render_create_page_with_years_and_semesters()
    {
        $response = $this->actingAs($this->teacher)->get(route('courses.create'));
        $response->assertStatus(200);
        $response->assertSee('Select Year');
        $response->assertSee('Select Semester');

        foreach (Year::cases() as $year) {
            $response->assertSee($year->value);
        }
        foreach (Semester::cases() as $semester) {
            $response->assertSee($semester->value);
        }
    }

    public function test_teacher_can_create_course()
    {
        $response = $this->actingAs($this->teacher)->post(route('courses.store'), [
            'code' => 'CSE102',
            'name' => 'Data Structures',
            'year' => '1st Year',
            'semester' => '2nd Semester',
        ]);

        $response->assertRedirect(route('courses.index'));
        $this->assertDatabaseHas('courses', [
            'code' => 'CSE102',
            'name' => 'Data Structures',
            'year' => '1st Year',
            'semester' => '2nd Semester',
            'department_id' => $this->department->id,
        ]);
    }

    public function test_year_and_semester_are_required_to_create_course()
    {
        $response = $this->actingAs($this->teacher)->post(route('courses.store'), [
            'code' => 'CSE102',
            'name' => 'Data Structures',
            'year' => '',
            'semester' => '',
        ]);

        $response->assertSessionHasErrors(['year', 'semester']);
        $this->assertDatabaseMissing('courses', [
            'code' => 'CSE102',
        ]);
    }

    public function test_teacher_cannot_create_duplicate_course_code()
    {
        Course::create([
            'user_id' => $this->teacher->id,
            'department_id' => $this->department->id,
            'code' => 'CSE101',
            'name' => 'Original Course',
            'year' => '1st Year',
            'semester' => '1st Semester',
        ]);

        $response = $this->actingAs($this->teacher)->post(route('courses.store'), [
            'code' => 'CSE101',
            'name' => 'Another Course',
            'year' => '1st Year',
            'semester' => '1st Semester',
        ]);

        $response->assertRedirect(route('courses.index'));
        $response->assertSessionHas('info', 'Duplicate Course Code');
    }

    public function test_teacher_can_render_edit_page_with_years_and_semesters()
    {
        $course = Course::create([
            'user_id' => $this->teacher->id,
            'department_id' => $this->department->id,
            'code' => 'CSE101',
            'name' => 'Old Name',
            'year' => '1st Year',
            'semester' => '1st Semester',
        ]);

        $response = $this->actingAs($this->teacher)->get(route('courses.edit', $course));
        $response->assertStatus(200);
        $response->assertSee('1st Year');
        $response->assertSee('1st Semester');
    }

    public function test_teacher_can_update_course()
    {
        $course = Course::create([
            'user_id' => $this->teacher->id,
            'department_id' => $this->department->id,
            'code' => 'CSE101',
            'name' => 'Old Name',
            'year' => '1st Year',
            'semester' => '1st Semester',
        ]);

        $response = $this->actingAs($this->teacher)->put(route('courses.update', ['course' => $course]), [
            'code' => 'CSE101',
            'name' => 'Updated Name',
            'year' => '2nd Year',
            'semester' => '1st Semester',
        ]);

        $response->assertRedirect(route('courses.index'));
        $this->assertDatabaseHas('courses', [
            'id' => $course->id,
            'name' => 'Updated Name',
            'year' => '2nd Year',
            'semester' => '1st Semester',
        ]);
    }
}
