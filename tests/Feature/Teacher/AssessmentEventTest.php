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
            'session' => '2026-2027',
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
            'session' => $this->group->session,
            'year' => $this->group->year->value,
            'semester' => $this->group->semester->value,
            'feedback_percentage' => 0,
        ]);

        $event = AssessmentEvent::first();
        $this->assertDatabaseHas('assessment_event_students', [
            'event_id' => $event->id,
            'student_id' => '19100001',
            'name' => 'Student One',
        ]);
    }

    public function test_create_view_filters_out_invalid_student_groups()
    {
        // Invalid: missing session
        $noSessionGroup = StudentGroup::create([
            'user_id' => $this->teacher->id,
            'department_id' => $this->department->id,
            'name' => 'No Session Group',
            'session' => null,
            'year' => '1st Year',
            'semester' => '1st Semester',
        ]);
        StudentGroupMember::create([
            'department_id' => $this->department->id,
            'group_id' => $noSessionGroup->id,
            'student_id' => '19100002',
            'name' => 'Student Two',
        ]);

        // Invalid: missing year
        $noYearGroup = StudentGroup::create([
            'user_id' => $this->teacher->id,
            'department_id' => $this->department->id,
            'name' => 'No Year Group',
            'session' => '2026-2027',
            'year' => null,
            'semester' => '1st Semester',
        ]);
        StudentGroupMember::create([
            'department_id' => $this->department->id,
            'group_id' => $noYearGroup->id,
            'student_id' => '19100003',
            'name' => 'Student Three',
        ]);

        // Invalid: 0 students
        $zeroStudentGroup = StudentGroup::create([
            'user_id' => $this->teacher->id,
            'department_id' => $this->department->id,
            'name' => 'Zero Students Group',
            'session' => '2026-2027',
            'year' => '2nd Year',
            'semester' => '1st Semester',
        ]);

        $response = $this->actingAs($this->teacher)->get(route('assessment_events.create'));
        $response->assertStatus(200);
        $response->assertSee($this->group->display_name);
        $response->assertDontSee('No Session Group');
        $response->assertDontSee('No Year Group');
        $response->assertDontSee('Zero Students Group');
    }

    public function test_cannot_create_assessment_event_with_empty_or_null_session_group()
    {
        $invalidGroup = StudentGroup::create([
            'user_id' => $this->teacher->id,
            'department_id' => $this->department->id,
            'name' => 'Invalid Session Group',
            'session' => null,
            'year' => '1st Year',
            'semester' => '1st Semester',
        ]);
        StudentGroupMember::create([
            'department_id' => $this->department->id,
            'group_id' => $invalidGroup->id,
            'student_id' => '19100099',
            'name' => 'Student 99',
        ]);

        $today = Carbon::now('Asia/Dhaka')->format(config('datetimeformat.date_format'));
        $tomorrow = Carbon::now('Asia/Dhaka')->addDays(2)->format(config('datetimeformat.date_format'));

        $response = $this->actingAs($this->teacher)->post(route('assessment_events.store'), [
            'teacher_id' => $this->teacher->id,
            'course_id' => $this->course->id,
            'group_id' => $invalidGroup->id,
            'start_date' => $today,
            'start_hour' => 10,
            'start_minute' => 0,
            'stop_date' => $tomorrow,
            'stop_hour' => 17,
            'stop_minute' => 0,
        ]);

        $response->assertSessionHasErrors('group_id');
        $this->assertDatabaseMissing('assessment_events', [
            'group_id' => $invalidGroup->id,
        ]);
    }

    public function test_cannot_create_assessment_event_with_zero_students_group()
    {
        $emptyGroup = StudentGroup::create([
            'user_id' => $this->teacher->id,
            'department_id' => $this->department->id,
            'name' => 'Empty Group',
            'session' => '2026-2027',
            'year' => '1st Year',
            'semester' => '1st Semester',
        ]);

        $today = Carbon::now('Asia/Dhaka')->format(config('datetimeformat.date_format'));
        $tomorrow = Carbon::now('Asia/Dhaka')->addDays(2)->format(config('datetimeformat.date_format'));

        $response = $this->actingAs($this->teacher)->post(route('assessment_events.store'), [
            'teacher_id' => $this->teacher->id,
            'course_id' => $this->course->id,
            'group_id' => $emptyGroup->id,
            'start_date' => $today,
            'start_hour' => 10,
            'start_minute' => 0,
            'stop_date' => $tomorrow,
            'stop_hour' => 17,
            'stop_minute' => 0,
        ]);

        $response->assertSessionHasErrors('group_id');
        $this->assertDatabaseMissing('assessment_events', [
            'group_id' => $emptyGroup->id,
        ]);
    }

    public function test_group_id_cannot_be_updated_on_assessment_event()
    {
        $today = Carbon::now('Asia/Dhaka')->format(config('datetimeformat.date_format'));
        $tomorrow = Carbon::now('Asia/Dhaka')->addDays(2)->format(config('datetimeformat.date_format'));

        $event = AssessmentEvent::create([
            'user_id' => $this->teacher->id,
            'department_id' => $this->department->id,
            'teacher_id' => $this->teacher->id,
            'course_id' => $this->course->id,
            'group_id' => $this->group->id,
            'start_time' => Carbon::now()->addDay(),
            'stop_time' => Carbon::now()->addDays(2),
        ]);

        $secondGroup = StudentGroup::create([
            'user_id' => $this->teacher->id,
            'department_id' => $this->department->id,
            'name' => 'Second Group',
            'session' => '2026-2027',
            'year' => '1st Year',
            'semester' => '1st Semester',
        ]);
        StudentGroupMember::create([
            'department_id' => $this->department->id,
            'group_id' => $secondGroup->id,
            'student_id' => '19100088',
            'name' => 'Student 88',
        ]);

        // Attempt direct model update on group_id
        $event->update([
            'group_id' => $secondGroup->id,
        ]);
        $event->refresh();
        $this->assertEquals($this->group->id, $event->group_id);

        // Attempt controller update passing different group_id
        $response = $this->actingAs($this->teacher)->put(route('assessment_events.update', $event), [
            'teacher_id' => $this->teacher->id,
            'course_id' => $this->course->id,
            'group_id' => $secondGroup->id,
            'start_date' => $today,
            'start_hour' => 10,
            'start_minute' => 0,
            'stop_date' => $tomorrow,
            'stop_hour' => 17,
            'stop_minute' => 0,
        ]);

        $response->assertRedirect(route('assessment_events.index'));
        $event->refresh();
        $this->assertEquals($this->group->id, $event->group_id);
    }

    public function test_edit_view_displays_disabled_fields()
    {
        $event = AssessmentEvent::create([
            'user_id' => $this->teacher->id,
            'department_id' => $this->department->id,
            'teacher_id' => $this->teacher->id,
            'course_id' => $this->course->id,
            'group_id' => $this->group->id,
            'start_time' => Carbon::now()->addDay(),
            'stop_time' => Carbon::now()->addDays(2),
        ]);

        $response = $this->actingAs($this->teacher)->get(route('assessment_events.edit', $event));
        $response->assertStatus(200);
        $response->assertSee('Teacher, course, and student group cannot be modified after event creation.');
        $response->assertSee($this->teacher->name);
        $response->assertSee($this->course->name);
        $response->assertSee($this->group->display_name);
    }

    public function test_only_time_can_be_updated_on_assessment_event()
    {
        $event = AssessmentEvent::create([
            'user_id' => $this->teacher->id,
            'department_id' => $this->department->id,
            'teacher_id' => $this->teacher->id,
            'course_id' => $this->course->id,
            'group_id' => $this->group->id,
            'session' => '2026-2027',
            'year' => '1st Year',
            'semester' => '1st Semester',
            'start_time' => Carbon::now('Asia/Dhaka')->addDay()->setHour(10)->setMinute(0),
            'stop_time' => Carbon::now('Asia/Dhaka')->addDays(2)->setHour(17)->setMinute(0),
        ]);

        $otherTeacher = User::factory()->create([
            'department_id' => $this->department->id,
            'role' => 'teacher',
        ]);
        $otherCourse = Course::create([
            'user_id' => $this->teacher->id,
            'department_id' => $this->department->id,
            'code' => 'CSE109',
            'name' => 'Data Structures',
        ]);
        $otherGroup = StudentGroup::create([
            'user_id' => $this->teacher->id,
            'department_id' => $this->department->id,
            'name' => 'Other Group',
            'session' => '2025-2026',
            'year' => '2nd Year',
            'semester' => '2nd Semester',
        ]);

        // Attempt direct model update on immutable fields
        $event->update([
            'teacher_id' => $otherTeacher->id,
            'course_id' => $otherCourse->id,
            'group_id' => $otherGroup->id,
        ]);
        $event->refresh();
        $this->assertEquals($this->teacher->id, $event->teacher_id);
        $this->assertEquals($this->course->id, $event->course_id);
        $this->assertEquals($this->group->id, $event->group_id);

        $newStart = Carbon::now('Asia/Dhaka')->addDays(3);
        $newStop = Carbon::now('Asia/Dhaka')->addDays(5);
        $startDateStr = $newStart->format(config('datetimeformat.date_format'));
        $stopDateStr = $newStop->format(config('datetimeformat.date_format'));

        // Attempt controller update passing different teacher, course, group alongside new time
        $response = $this->actingAs($this->teacher)->put(route('assessment_events.update', $event), [
            'teacher_id' => $otherTeacher->id,
            'course_id' => $otherCourse->id,
            'group_id' => $otherGroup->id,
            'start_date' => $startDateStr,
            'start_hour' => 11,
            'start_minute' => 30,
            'stop_date' => $stopDateStr,
            'stop_hour' => 16,
            'stop_minute' => 45,
        ]);

        $response->assertRedirect(route('assessment_events.index'));
        $event->refresh();

        // Time is updated
        $this->assertEquals(11, Carbon::parse($event->start_time)->hour);
        $this->assertEquals(30, Carbon::parse($event->start_time)->minute);
        $this->assertEquals(16, Carbon::parse($event->stop_time)->hour);
        $this->assertEquals(45, Carbon::parse($event->stop_time)->minute);

        // Teacher, course, group, session, year, semester remain untouched
        $this->assertEquals($this->teacher->id, $event->teacher_id);
        $this->assertEquals($this->course->id, $event->course_id);
        $this->assertEquals($this->group->id, $event->group_id);
        $this->assertEquals('2026-2027', $event->session);
        $this->assertEquals('1st Year', $event->year->value);
        $this->assertEquals('1st Semester', $event->semester->value);
    }

    public function test_stamped_session_year_and_semester_cannot_be_edited_later()
    {
        $today = Carbon::now('Asia/Dhaka')->format(config('datetimeformat.date_format'));
        $tomorrow = Carbon::now('Asia/Dhaka')->addDays(2)->format(config('datetimeformat.date_format'));

        $event = AssessmentEvent::create([
            'user_id' => $this->teacher->id,
            'department_id' => $this->department->id,
            'teacher_id' => $this->teacher->id,
            'course_id' => $this->course->id,
            'group_id' => $this->group->id,
            'session' => '2026-2027',
            'year' => '1st Year',
            'semester' => '1st Semester',
            'start_time' => Carbon::now()->addDay(),
            'stop_time' => Carbon::now()->addDays(2),
        ]);

        // Attempt direct model update
        $event->update([
            'session' => '1999-2000',
            'year' => '4th Year',
            'semester' => '2nd Semester',
        ]);

        $event->refresh();
        $this->assertEquals('2026-2027', $event->session);
        $this->assertEquals('1st Year', $event->year->value);
        $this->assertEquals('1st Semester', $event->semester->value);

        // Attempt controller update with a new valid group having different session/year/semester
        $secondGroup = StudentGroup::create([
            'user_id' => $this->teacher->id,
            'department_id' => $this->department->id,
            'name' => 'Second Group',
            'session' => '2024-2025',
            'year' => '2nd Year',
            'semester' => '2nd Semester',
        ]);
        StudentGroupMember::create([
            'department_id' => $this->department->id,
            'group_id' => $secondGroup->id,
            'student_id' => '19100088',
            'name' => 'Student 88',
        ]);

        $response = $this->actingAs($this->teacher)->put(route('assessment_events.update', $event), [
            'teacher_id' => $this->teacher->id,
            'course_id' => $this->course->id,
            'group_id' => $secondGroup->id,
            'start_date' => $today,
            'start_hour' => 10,
            'start_minute' => 0,
            'stop_date' => $tomorrow,
            'stop_hour' => 17,
            'stop_minute' => 0,
        ]);

        $response->assertRedirect(route('assessment_events.index'));
        $event->refresh();
        // The stamped session, year, semester remain permanently stamped
        $this->assertEquals('2026-2027', $event->session);
        $this->assertEquals('1st Year', $event->year->value);
        $this->assertEquals('1st Semester', $event->semester->value);
        $this->assertEquals($this->group->id, $event->group_id);
    }
}
