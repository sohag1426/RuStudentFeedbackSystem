<?php

namespace Tests\Feature\Teacher;

use App\Models\AssessmentEvent;
use App\Models\AssessmentEventStudent;
use App\Models\Course;
use App\Models\Department;
use App\Models\StudentGroup;
use App\Models\StudentGroupMember;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssessmentEventTest extends TestCase
{
    use RefreshDatabase;

    protected $department;
    protected $teacher;
    protected $course;
    protected $group;

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

        $this->course = Course::create([
            'user_id' => $this->teacher->id,
            'department_id' => $this->department->id,
            'code' => 'CSE101',
            'name' => 'Structured Programming',
        ]);

        $this->group = StudentGroup::create([
            'user_id' => $this->teacher->id,
            'department_id' => $this->department->id,
            'name' => '2023-1',
            'year' => '1st Year',
            'semester' => '1st Semester',
        ]);

        StudentGroupMember::create([
            'department_id' => $this->department->id,
            'group_id' => $this->group->id,
            'student_id' => '19100001',
            'name' => 'Student One',
        ]);
    }

    public function test_teacher_can_view_assessment_events()
    {
        AssessmentEvent::create([
            'user_id' => $this->teacher->id,
            'department_id' => $this->department->id,
            'teacher_id' => $this->teacher->id,
            'course_id' => $this->course->id,
            'group_id' => $this->group->id,
            'start_time' => Carbon::now()->subDays(1),
            'stop_time' => Carbon::now()->addDays(2),
        ]);

        $response = $this->actingAs($this->teacher)->get(route('assessment_events.index'));
        $response->assertStatus(200);
        $response->assertSee('CSE101');
    }

    public function test_teacher_can_create_assessment_event_and_populates_students()
    {
        $today = Carbon::now('Asia/Dhaka')->format(config('datetimeformat.date_format'));
        $tomorrow = Carbon::now('Asia/Dhaka')->addDays(2)->format(config('datetimeformat.date_format'));

        $response = $this->actingAs($this->teacher)->post(route('assessment_events.store'), [
            'teacher_id' => $this->teacher->id,
            'course_id' => $this->course->id,
            'group_id' => $this->group->id,
            'start_date' => $today,
            'start_hour' => 10,
            'start_minute' => 0,
            'stop_date' => $tomorrow,
            'stop_hour' => 17,
            'stop_minute' => 0,
        ]);

        $response->assertRedirect(route('assessment_events.index'));
        $this->assertDatabaseHas('assessment_events', [
            'teacher_id' => $this->teacher->id,
            'course_id' => $this->course->id,
            'group_id' => $this->group->id,
        ]);

        $event = AssessmentEvent::first();
        $this->assertDatabaseHas('assessment_event_students', [
            'event_id' => $event->id,
            'student_id' => '19100001',
            'name' => 'Student One',
        ]);
    }
}
