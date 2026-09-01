<?php

namespace Tests\Feature\Teacher;

use App\Models\Department;
use App\Models\StudentGroup;
use App\Models\User;
use App\Services\SessionService;
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

    public function test_student_group_session_defaults_to_null()
    {
        $group = new StudentGroup();
        $this->assertNull($group->session);

        $savedGroup = StudentGroup::create([
            'user_id' => $this->teacher->id,
            'department_id' => $this->department->id,
            'name' => 'Batch 2023',
            'year' => '1st Year',
            'semester' => '1st Semester',
        ]);

        $this->assertNull($savedGroup->session);
        $this->assertDatabaseHas('student_groups', [
            'id' => $savedGroup->id,
            'session' => null,
        ]);
    }

    public function test_teacher_can_view_student_groups_with_session()
    {
        StudentGroup::create([
            'user_id' => $this->teacher->id,
            'department_id' => $this->department->id,
            'name' => 'Batch 2023',
            'year' => '1st Year',
            'semester' => '1st Semester',
            'session' => '2025-2026',
        ]);

        $response = $this->actingAs($this->teacher)->get(route('student_groups.index'));
        $response->assertStatus(200);
        $response->assertSee('Batch 2023');
        $response->assertSee('2025-2026');
    }

    public function test_teacher_can_render_create_page_with_sessions()
    {
        $response = $this->actingAs($this->teacher)->get(route('student_groups.create'));
        $response->assertStatus(200);
        $response->assertSee('Select Session');

        $sessions = SessionService::getSessions();
        foreach ($sessions as $session) {
            $response->assertSee($session);
        }
    }

    public function test_session_is_mandatory_during_create()
    {
        $response = $this->actingAs($this->teacher)->post(route('student_groups.store'), [
            'name' => 'Batch 2024',
            'year' => '1st Year',
            'semester' => '2nd Semester',
            'session' => '',
        ]);

        $response->assertSessionHasErrors('session');
        $this->assertDatabaseMissing('student_groups', [
            'name' => 'Batch 2024',
        ]);
    }

    public function test_teacher_can_create_student_group_with_session()
    {
        $response = $this->actingAs($this->teacher)->post(route('student_groups.store'), [
            'name' => 'Batch 2024',
            'year' => '1st Year',
            'semester' => '2nd Semester',
            'session' => '2026-2027',
        ]);

        $response->assertRedirect(route('student_groups.index'));
        $this->assertDatabaseHas('student_groups', [
            'name' => 'Batch 2024',
            'year' => '1st Year',
            'semester' => '2nd Semester',
            'session' => '2026-2027',
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
            'session' => '2025-2026',
        ]);

        $response = $this->actingAs($this->teacher)->post(route('student_groups.store'), [
            'name' => 'Batch 2023',
            'year' => '1st Year',
            'semester' => '1st Semester',
            'session' => '2025-2026',
        ]);

        $response->assertRedirect(route('student_groups.index'));
        $response->assertSessionHas('info', 'Duplicate Student Group');
    }

    public function test_teacher_can_render_edit_page_with_session()
    {
        $group = StudentGroup::create([
            'user_id' => $this->teacher->id,
            'department_id' => $this->department->id,
            'name' => 'Batch 2023',
            'year' => '1st Year',
            'semester' => '1st Semester',
            'session' => '2024-2025',
        ]);

        $response = $this->actingAs($this->teacher)->get(route('student_groups.edit', $group));
        $response->assertStatus(200);
        $response->assertSee('2024-2025');
    }

    public function test_session_is_mandatory_during_edit()
    {
        $group = StudentGroup::create([
            'user_id' => $this->teacher->id,
            'department_id' => $this->department->id,
            'name' => 'Batch 2023',
            'year' => '1st Year',
            'semester' => '1st Semester',
            'session' => '2024-2025',
        ]);

        $response = $this->actingAs($this->teacher)->put(route('student_groups.update', $group), [
            'name' => 'Batch 2023 Updated',
            'year' => '1st Year',
            'semester' => '1st Semester',
            'session' => '',
        ]);

        $response->assertSessionHasErrors('session');
        $group->refresh();
        $this->assertEquals('Batch 2023', $group->name);
        $this->assertEquals('2024-2025', $group->session);
    }

    public function test_teacher_can_update_student_group_session()
    {
        $group = StudentGroup::create([
            'user_id' => $this->teacher->id,
            'department_id' => $this->department->id,
            'name' => 'Batch 2023',
            'year' => '1st Year',
            'semester' => '1st Semester',
            'session' => '2024-2025',
        ]);

        $response = $this->actingAs($this->teacher)->put(route('student_groups.update', $group), [
            'name' => 'Batch 2023 Updated',
            'year' => '2nd Year',
            'semester' => '1st Semester',
            'session' => '2025-2026',
        ]);

        $response->assertRedirect(route('student_groups.index'));
        $group->refresh();
        $this->assertEquals('Batch 2023 Updated', $group->name);
        $this->assertEquals('2025-2026', $group->session);
    }

    public function test_display_name_strictly_uses_session_year_and_semester()
    {
        $group = new StudentGroup([
            'name' => 'Legacy Name Should Not Appear',
            'session' => '2026-2027',
            'year' => '1st Year',
            'semester' => '1st Semester',
        ]);

        $this->assertEquals('2026-2027, 1st Year, 1st Semester', $group->display_name);
        $this->assertStringNotContainsString('Legacy Name', $group->display_name);
    }

    public function test_display_name_fallback_to_name_when_attributes_are_empty()
    {
        $group = new StudentGroup([
            'name' => 'Old Legacy Group',
            'session' => null,
            'year' => null,
            'semester' => null,
        ]);

        $this->assertEquals('Old Legacy Group', $group->display_name);
    }
}
