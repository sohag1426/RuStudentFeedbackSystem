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
        $group = new StudentGroup;
        $this->assertNull($group->session);

        $savedGroup = StudentGroup::create([
            'user_id' => $this->teacher->id,
            'department_id' => $this->department->id,
            'name' => 'Batch 2023',
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
            'session' => '2026-2027',
        ]);

        $response->assertRedirect(route('student_groups.index'));
        $this->assertDatabaseHas('student_groups', [
            'name' => 'Batch 2024',
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
            'session' => '2025-2026',
        ]);

        $response = $this->actingAs($this->teacher)->post(route('student_groups.store'), [
            'name' => 'Batch 2023',
            'session' => '2025-2026',
        ]);

        $response->assertRedirect(route('student_groups.index'));
        $response->assertSessionHas('info', 'Duplicate Student Group');
    }

    public function test_teacher_cannot_create_student_group_with_same_session_different_name()
    {
        StudentGroup::create([
            'user_id' => $this->teacher->id,
            'department_id' => $this->department->id,
            'name' => 'Original Group',
            'session' => '2025-2026',
        ]);

        $response = $this->actingAs($this->teacher)->post(route('student_groups.store'), [
            'name' => 'Completely Different Name',
            'session' => '2025-2026',
        ]);

        $response->assertRedirect(route('student_groups.index'));
        $response->assertSessionHas('info', 'Duplicate Student Group');
        $this->assertEquals(1, StudentGroup::where('department_id', $this->department->id)->where('session', '2025-2026')->count());
    }

    public function test_student_group_policy_blocks_existing_session()
    {
        StudentGroup::create([
            'user_id' => $this->teacher->id,
            'department_id' => $this->department->id,
            'name' => 'Existing Group',
            'session' => '2025-2026',
        ]);

        $this->assertFalse($this->teacher->can('create', [StudentGroup::class, '2025-2026']));
        $this->assertFalse($this->teacher->can('createForSession', [StudentGroup::class, '2025-2026']));
        $this->assertTrue($this->teacher->can('create', [StudentGroup::class, '2027-2028']));
        $this->assertTrue($this->teacher->can('createForSession', [StudentGroup::class, '2027-2028']));
    }

    public function test_teacher_cannot_update_student_group_to_another_groups_session()
    {
        $group1 = StudentGroup::create([
            'user_id' => $this->teacher->id,
            'department_id' => $this->department->id,
            'name' => 'Group 1',
            'session' => '2024-2025',
        ]);

        $group2 = StudentGroup::create([
            'user_id' => $this->teacher->id,
            'department_id' => $this->department->id,
            'name' => 'Group 2',
            'session' => '2025-2026',
        ]);

        $response = $this->actingAs($this->teacher)->put(route('student_groups.update', $group2), [
            'name' => 'Group 2 Updated',
            'session' => '2024-2025',
        ]);

        $response->assertRedirect(route('student_groups.index'));
        $response->assertSessionHas('info', 'Duplicate Student Group');
        $group2->refresh();
        $this->assertEquals('2025-2026', $group2->session);
    }

    public function test_teacher_can_render_edit_page_with_session()
    {
        $group = StudentGroup::create([
            'user_id' => $this->teacher->id,
            'department_id' => $this->department->id,
            'name' => 'Batch 2023',
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
            'session' => '2024-2025',
        ]);

        $response = $this->actingAs($this->teacher)->put(route('student_groups.update', $group), [
            'name' => 'Batch 2023 Updated',
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
            'session' => '2024-2025',
        ]);

        $response = $this->actingAs($this->teacher)->put(route('student_groups.update', $group), [
            'name' => 'Batch 2023 Updated',
            'session' => '2025-2026',
        ]);

        $response->assertRedirect(route('student_groups.index'));
        $group->refresh();
        $this->assertEquals('Batch 2023 Updated', $group->name);
        $this->assertEquals('2025-2026', $group->session);
    }

    public function test_display_name_uses_name_and_session()
    {
        $group = new StudentGroup([
            'name' => 'Batch A',
            'session' => '2026-2027',
        ]);

        $this->assertEquals('Batch A - 2026-2027', $group->display_name);
    }

    public function test_display_name_fallback_to_name_when_session_is_empty()
    {
        $group = new StudentGroup([
            'name' => 'Old Legacy Group',
            'session' => null,
        ]);

        $this->assertEquals('Old Legacy Group', $group->display_name);
    }

    public function test_cannot_create_student_group_when_null_session_exists_in_department()
    {
        StudentGroup::create([
            'user_id' => $this->teacher->id,
            'department_id' => $this->department->id,
            'name' => 'Legacy Group With Null Session',
            'session' => null,
        ]);

        $this->assertTrue(StudentGroup::hasNullSession($this->department->id));
        $this->assertFalse($this->teacher->can('create', StudentGroup::class));

        // GET create page is blocked and redirected
        $response = $this->actingAs($this->teacher)->get(route('student_groups.create'));
        $response->assertRedirect(route('student_groups.index'));
        $response->assertSessionHas('info');

        // POST store is blocked and redirected
        $storeResponse = $this->actingAs($this->teacher)->post(route('student_groups.store'), [
            'name' => 'New Group Attempt',
            'session' => '2026-2027',
        ]);
        $storeResponse->assertRedirect(route('student_groups.index'));
        $storeResponse->assertSessionHas('info');

        $this->assertDatabaseMissing('student_groups', [
            'name' => 'New Group Attempt',
        ]);

        // Index page hides the Create button and shows warning alert
        $indexResponse = $this->actingAs($this->teacher)->get(route('student_groups.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertDontSee('Create Student Group');
        $indexResponse->assertSee('Action required:');
    }

    public function test_can_create_student_group_once_null_session_is_resolved()
    {
        $group = StudentGroup::create([
            'user_id' => $this->teacher->id,
            'department_id' => $this->department->id,
            'name' => 'Legacy Group',
            'session' => null,
        ]);

        $this->assertTrue(StudentGroup::hasNullSession($this->department->id));

        // Assign a session to the legacy group
        $group->session = '2024-2025';
        $group->save();

        $this->assertFalse(StudentGroup::hasNullSession($this->department->id));
        $this->assertTrue($this->teacher->can('create', StudentGroup::class));

        // Now creating a new student group with a different session works
        $response = $this->actingAs($this->teacher)->post(route('student_groups.store'), [
            'name' => 'New Group 2026',
            'session' => '2026-2027',
        ]);

        $response->assertRedirect(route('student_groups.index'));
        $this->assertDatabaseHas('student_groups', [
            'name' => 'New Group 2026',
            'session' => '2026-2027',
            'department_id' => $this->department->id,
        ]);
    }
}
