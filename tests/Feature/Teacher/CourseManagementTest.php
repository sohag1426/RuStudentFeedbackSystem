<?php

namespace Tests\Feature\Teacher;

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
        ]);

        $response = $this->actingAs($this->teacher)->get(route('courses.index'));
        $response->assertStatus(200);
        $response->assertSee('CSE101');
    }

    public function test_teacher_can_create_course()
    {
        $response = $this->actingAs($this->teacher)->post(route('courses.store'), [
            'code' => 'CSE102',
            'name' => 'Data Structures',
        ]);

        $response->assertRedirect(route('courses.index'));
        $this->assertDatabaseHas('courses', [
            'code' => 'CSE102',
            'name' => 'Data Structures',
            'department_id' => $this->department->id,
        ]);
    }

    public function test_teacher_cannot_create_duplicate_course_code()
    {
        Course::create([
            'user_id' => $this->teacher->id,
            'department_id' => $this->department->id,
            'code' => 'CSE101',
            'name' => 'Original Course',
        ]);

        $response = $this->actingAs($this->teacher)->post(route('courses.store'), [
            'code' => 'CSE101',
            'name' => 'Another Course',
        ]);

        $response->assertRedirect(route('courses.index'));
        $response->assertSessionHas('info', 'Duplicate Course Code');
    }

    public function test_teacher_can_update_course()
    {
        $course = Course::create([
            'user_id' => $this->teacher->id,
            'department_id' => $this->department->id,
            'code' => 'CSE101',
            'name' => 'Old Name',
        ]);

        $response = $this->actingAs($this->teacher)->put(route('courses.update', ['course' => $course]), [
            'code' => 'CSE101',
            'name' => 'Updated Name',
        ]);

        $response->assertRedirect(route('courses.index'));
        $this->assertDatabaseHas('courses', [
            'id' => $course->id,
            'name' => 'Updated Name',
        ]);
    }
}
