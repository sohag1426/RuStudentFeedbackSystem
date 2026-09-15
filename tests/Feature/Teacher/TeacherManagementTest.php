<?php

namespace Tests\Feature\Teacher;

use App\Models\Department;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherManagementTest extends TestCase
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
            'internet_id' => '12345678',
            'name' => 'Dr. Test Teacher',
            'email' => 'teacher@ru.ac.bd',
            'mobile' => '01700000000',
        ]);
    }

    public function test_teacher_can_view_teachers_list_with_internet_id()
    {
        $response = $this->actingAs($this->teacher)->get(route('users.index'));

        $response->assertStatus(200);
        $response->assertSee('#');
        $response->assertSee((string) $this->teacher->id);
        $response->assertSee('Internet ID');
        $response->assertSee('12345678');
        $response->assertSee('Dr. Test Teacher');
        $response->assertSee('teacher@ru.ac.bd');
        $response->assertSee('01700000000');
    }

    public function test_teacher_can_create_new_teacher_with_internet_id()
    {
        $response = $this->actingAs($this->teacher)->post(route('users.store'), [
            'internet_id' => '87654321',
            'name' => 'New Teacher',
            'email' => 'newteacher@ru.ac.bd',
            'mobile' => '01800000000',
        ]);

        $response->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', [
            'internet_id' => '87654321',
            'name' => 'New Teacher',
            'email' => 'newteacher@ru.ac.bd',
            'mobile' => '01800000000',
            'department_id' => $this->department->id,
        ]);
    }
}
