<?php

namespace Tests\Feature\Teacher;

use App\Models\Department;
use App\Models\StudentGroup;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentGroupTest extends TestCase
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

    public function test_teacher_can_view_student_groups()
    {
        StudentGroup::create([
            'user_id' => $this->teacher->id,
            'department_id' => $this->department->id,
            'name' => 'Batch 2023',
            'year' => '1st Year',
            'semester' => '1st Semester',
        ]);

        $response = $this->actingAs($this->teacher)->get(route('student_groups.index'));
        $response->assertStatus(200);
        $response->assertSee('Batch 2023');
    }

    public function test_teacher_can_create_student_group()
    {
        $response = $this->actingAs($this->teacher)->post(route('student_groups.store'), [
            'name' => 'Batch 2024',
            'year' => '1st Year',
            'semester' => '2nd Semester',
        ]);

        $response->assertRedirect(route('student_groups.index'));
        $this->assertDatabaseHas('student_groups', [
            'name' => 'Batch 2024',
            'year' => '1st Year',
            'semester' => '2nd Semester',
            'department_id' => $this->department->id,
        ]);
    }

    public function test_teacher_cannot_create_duplicate_student_group()
    {
        StudentGroup::create([
            'user_id' => $this->teacher->id,
            'department_id' => $this->department->id,
            'name' => 'Batch 2023',
            'year' => '1st Year',
            'semester' => '1st Semester',
        ]);

        $response = $this->actingAs($this->teacher)->post(route('student_groups.store'), [
            'name' => 'Batch 2023',
            'year' => '1st Year',
            'semester' => '1st Semester',
        ]);

        $response->assertRedirect(route('student_groups.index'));
        $response->assertSessionHas('info', 'Duplicate Student Group');
    }
}
