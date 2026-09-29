<?php

namespace Tests\Feature\Command;

use App\Models\Course;
use App\Models\Department;
use App\Models\StudentGroup;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TempUpdateAssessmentEventsCommandTest extends TestCase
{
    use RefreshDatabase;

    private Department $department;

    private User $teacher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->department = Department::create(['en_name' => 'Department of CSE']);
        $this->teacher = User::factory()->create([
            'department_id' => $this->department->id,
            'role' => 'teacher',
        ]);
    }

    public function test_updates_when_assessment_event_values_are_null(): void
    {
        $course = Course::create([
            'user_id' => $this->teacher->id,
            'department_id' => $this->department->id,
            'code' => 'CSE101',
            'name' => 'Programming',
            'year' => '1st Year',
            'semester' => '1st Semester',
        ]);

        $group = StudentGroup::create([
            'user_id' => $this->teacher->id,
            'department_id' => $this->department->id,
            'name' => 'CSE 2024',
            'session' => '2024-2025',
        ]);

        $eventId = DB::table('assessment_events')->insertGetId([
            'user_id' => $this->teacher->id,
            'department_id' => $this->department->id,
            'teacher_id' => $this->teacher->id,
            'course_id' => $course->id,
            'group_id' => $group->id,
            'session' => null,
            'year' => null,
            'semester' => null,
            'start_time' => Carbon::now(),
            'stop_time' => Carbon::now()->addDays(2),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->artisan('app:temp-update-assessment-events')
            ->expectsOutputToContain('Events updated: 1')
            ->assertSuccessful();

        $this->assertDatabaseHas('assessment_events', [
            'id' => $eventId,
            'session' => '2024-2025',
            'year' => '1st Year',
            'semester' => '1st Semester',
        ]);
    }

    public function test_updates_when_assessment_event_values_mismatch(): void
    {
        $course = Course::create([
            'user_id' => $this->teacher->id,
            'department_id' => $this->department->id,
            'code' => 'CSE201',
            'name' => 'Data Structures',
            'year' => '2nd Year',
            'semester' => '2nd Semester',
        ]);

        $group = StudentGroup::create([
            'user_id' => $this->teacher->id,
            'department_id' => $this->department->id,
            'name' => 'CSE 2023',
            'session' => '2023-2024',
        ]);

        $eventId = DB::table('assessment_events')->insertGetId([
            'user_id' => $this->teacher->id,
            'department_id' => $this->department->id,
            'teacher_id' => $this->teacher->id,
            'course_id' => $course->id,
            'group_id' => $group->id,
            'session' => '2020-2021', // mismatch
            'year' => '1st Year',     // mismatch
            'semester' => '1st Semester', // mismatch
            'start_time' => Carbon::now(),
            'stop_time' => Carbon::now()->addDays(2),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->artisan('app:temp-update-assessment-events')
            ->expectsOutputToContain('Events updated: 1')
            ->assertSuccessful();

        $this->assertDatabaseHas('assessment_events', [
            'id' => $eventId,
            'session' => '2023-2024',
            'year' => '2nd Year',
            'semester' => '2nd Semester',
        ]);
    }

    public function test_skips_when_source_has_null_value(): void
    {
        $course = Course::create([
            'user_id' => $this->teacher->id,
            'department_id' => $this->department->id,
            'code' => 'CSE301',
            'name' => 'Operating Systems',
            'year' => null,
            'semester' => null,
        ]);

        $group = StudentGroup::create([
            'user_id' => $this->teacher->id,
            'department_id' => $this->department->id,
            'name' => 'CSE Legacy',
            'session' => null,
        ]);

        $eventId = DB::table('assessment_events')->insertGetId([
            'user_id' => $this->teacher->id,
            'department_id' => $this->department->id,
            'teacher_id' => $this->teacher->id,
            'course_id' => $course->id,
            'group_id' => $group->id,
            'session' => 'Existing Session',
            'year' => '3rd Year',
            'semester' => '1st Semester',
            'start_time' => Carbon::now(),
            'stop_time' => Carbon::now()->addDays(2),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->artisan('app:temp-update-assessment-events')
            ->expectsOutputToContain('Events updated: 0')
            ->expectsOutputToContain('Events skipped (no changes / source null): 1')
            ->assertSuccessful();

        // Values should not be cleared to null
        $this->assertDatabaseHas('assessment_events', [
            'id' => $eventId,
            'session' => 'Existing Session',
            'year' => '3rd Year',
            'semester' => '1st Semester',
        ]);
    }

    public function test_skips_when_values_are_identical(): void
    {
        $course = Course::create([
            'user_id' => $this->teacher->id,
            'department_id' => $this->department->id,
            'code' => 'CSE401',
            'name' => 'AI',
            'year' => '4th Year',
            'semester' => '1st Semester',
        ]);

        $group = StudentGroup::create([
            'user_id' => $this->teacher->id,
            'department_id' => $this->department->id,
            'name' => 'CSE 2021',
            'session' => '2021-2022',
        ]);

        $eventId = DB::table('assessment_events')->insertGetId([
            'user_id' => $this->teacher->id,
            'department_id' => $this->department->id,
            'teacher_id' => $this->teacher->id,
            'course_id' => $course->id,
            'group_id' => $group->id,
            'session' => '2021-2022',
            'year' => '4th Year',
            'semester' => '1st Semester',
            'start_time' => Carbon::now(),
            'stop_time' => Carbon::now()->addDays(2),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->artisan('app:temp-update-assessment-events')
            ->expectsOutputToContain('Events updated: 0')
            ->expectsOutputToContain('Events skipped (no changes / source null): 1')
            ->assertSuccessful();

        $this->assertDatabaseHas('assessment_events', [
            'id' => $eventId,
            'session' => '2021-2022',
            'year' => '4th Year',
            'semester' => '1st Semester',
        ]);
    }

    public function test_dry_run_does_not_persist_changes(): void
    {
        $course = Course::create([
            'user_id' => $this->teacher->id,
            'department_id' => $this->department->id,
            'code' => 'CSE101',
            'name' => 'Programming',
            'year' => '1st Year',
            'semester' => '1st Semester',
        ]);

        $group = StudentGroup::create([
            'user_id' => $this->teacher->id,
            'department_id' => $this->department->id,
            'name' => 'CSE 2024',
            'session' => '2024-2025',
        ]);

        $eventId = DB::table('assessment_events')->insertGetId([
            'user_id' => $this->teacher->id,
            'department_id' => $this->department->id,
            'teacher_id' => $this->teacher->id,
            'course_id' => $course->id,
            'group_id' => $group->id,
            'session' => null,
            'year' => null,
            'semester' => null,
            'start_time' => Carbon::now(),
            'stop_time' => Carbon::now()->addDays(2),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->artisan('app:temp-update-assessment-events --dry-run')
            ->expectsOutputToContain('DRY RUN MODE ENABLED')
            ->expectsOutputToContain('Events eligible for update: 1')
            ->assertSuccessful();

        // Database should still have nulls
        $this->assertDatabaseHas('assessment_events', [
            'id' => $eventId,
            'session' => null,
            'year' => null,
            'semester' => null,
        ]);
    }
}
