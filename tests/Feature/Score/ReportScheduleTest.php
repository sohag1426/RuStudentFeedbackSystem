<?php

namespace Tests\Feature\Score;

use App\Models\Assessment;
use App\Models\AssessmentEvent;
use App\Models\AssessmentEventStudent;
use App\Models\AssessmentStatus;
use App\Models\Course;
use App\Models\Department;
use App\Models\Question;
use App\Models\QuestionsGroup;
use App\Models\StudentGroup;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportScheduleTest extends TestCase
{
    use RefreshDatabase;

    private $department;

    private $teacher;

    private $course;

    private $group;

    private $question;

    protected function setUp(): void
    {
        parent::setUp();

        $this->department = Department::create(['en_name' => 'Department of CSE']);
        $this->teacher = User::factory()->create(['department_id' => $this->department->id]);
        $this->course = Course::create([
            'user_id' => $this->teacher->id,
            'department_id' => $this->department->id,
            'code' => 'CSE101',
            'name' => 'Structured Programming',
        ]);
        $this->group = StudentGroup::create([
            'user_id' => $this->teacher->id,
            'department_id' => $this->department->id,
            'name' => 'CSE 2026',
            'session' => '2026-2027',
            'year' => '1st Year',
            'semester' => '1st Semester',
        ]);

        $qGroup = QuestionsGroup::create(['en_name' => 'General Quality', 'bn_name' => 'মান']);
        $this->question = Question::create([
            'department_id' => $this->department->id,
            'questions_group_id' => $qGroup->id,
            'en' => 'Q1',
            'bn' => 'প্র১',
        ]);
    }

    private function createEventWithAssessment(Carbon $startTime, Carbon $stopTime, int $score = 5): AssessmentEvent
    {
        $event = AssessmentEvent::create([
            'user_id' => $this->teacher->id,
            'department_id' => $this->department->id,
            'teacher_id' => $this->teacher->id,
            'course_id' => $this->course->id,
            'group_id' => $this->group->id,
            'start_time' => $startTime,
            'stop_time' => $stopTime,
        ]);

        AssessmentEventStudent::create([
            'event_id' => $event->id,
            'department_id' => $this->department->id,
            'group_id' => $this->group->id,
            'student_id' => '20260001',
            'name' => 'Student 1',
        ]);

        Assessment::create([
            'department_id' => $this->department->id,
            'event_id' => $event->id,
            'question_id' => $this->question->id,
            'score' => $score,
        ]);

        AssessmentStatus::create([
            'department_id' => $this->department->id,
            'event_id' => $event->id,
            'student_id' => '20260001',
            'status' => 1,
        ]);

        return $event;
    }

    public function test_scope_running_identifies_running_events()
    {
        $now = Carbon::parse('2026-09-08 12:00:00');

        $running = $this->createEventWithAssessment(
            $now->copy()->subDays(2),
            $now->copy()->addDays(2)
        );

        $endedYesterday = $this->createEventWithAssessment(
            $now->copy()->subDays(5),
            $now->copy()->subDay()->setTime(17, 0, 0)
        );

        $upcoming = $this->createEventWithAssessment(
            $now->copy()->addDay(),
            $now->copy()->addDays(5)
        );

        $runningEvents = AssessmentEvent::running($now)->pluck('id')->all();

        $this->assertContains($running->id, $runningEvents);
        $this->assertNotContains($endedYesterday->id, $runningEvents);
        $this->assertNotContains($upcoming->id, $runningEvents);
    }

    public function test_scope_ended_one_day_before_identifies_events_ended_yesterday_or_today()
    {
        $now = Carbon::parse('2026-09-08 12:00:00');

        $endedYesterday = $this->createEventWithAssessment(
            $now->copy()->subDays(5),
            $now->copy()->subDay()->setTime(16, 0, 0)
        );

        $endedEarlierToday = $this->createEventWithAssessment(
            $now->copy()->subDays(3),
            $now->copy()->setTime(8, 0, 0)
        );

        $endedTwoDaysAgo = $this->createEventWithAssessment(
            $now->copy()->subDays(7),
            $now->copy()->subDays(2)->setTime(18, 0, 0)
        );

        $running = $this->createEventWithAssessment(
            $now->copy()->subDays(2),
            $now->copy()->addDays(2)
        );

        $endedEvents = AssessmentEvent::endedOneDayBefore($now)->pluck('id')->all();

        $this->assertContains($endedYesterday->id, $endedEvents);
        $this->assertContains($endedEarlierToday->id, $endedEvents);
        $this->assertNotContains($endedTwoDaysAgo->id, $endedEvents);
        $this->assertNotContains($running->id, $endedEvents);
    }

    public function test_scope_running_or_ended_one_day_before_filters_properly()
    {
        $now = Carbon::parse('2026-09-08 12:00:00');

        $running = $this->createEventWithAssessment(
            $now->copy()->subDays(2),
            $now->copy()->addDays(2)
        );

        $endedYesterday = $this->createEventWithAssessment(
            $now->copy()->subDays(5),
            $now->copy()->subDay()->setTime(18, 0, 0)
        );

        $endedTwoDaysAgo = $this->createEventWithAssessment(
            $now->copy()->subDays(10),
            $now->copy()->subDays(2)->setTime(12, 0, 0)
        );

        $upcoming = $this->createEventWithAssessment(
            $now->copy()->addDay(),
            $now->copy()->addDays(3)
        );

        $filteredEvents = AssessmentEvent::runningOrEndedOneDayBefore($now)->pluck('id')->all();

        $this->assertContains($running->id, $filteredEvents);
        $this->assertContains($endedYesterday->id, $filteredEvents);
        $this->assertNotContains($endedTwoDaysAgo->id, $filteredEvents);
        $this->assertNotContains($upcoming->id, $filteredEvents);
    }

    public function test_report_generate_command_only_generates_for_running_and_ended_one_day_before()
    {
        Carbon::setTestNow('2026-09-08 12:00:00');

        $running = $this->createEventWithAssessment(
            Carbon::now()->subDays(2),
            Carbon::now()->addDays(2),
            5
        );

        $endedYesterday = $this->createEventWithAssessment(
            Carbon::now()->subDays(5),
            Carbon::now()->subDay()->setTime(17, 0, 0),
            4
        );

        $endedLongAgo = $this->createEventWithAssessment(
            Carbon::now()->subDays(30),
            Carbon::now()->subDays(5),
            3
        );

        $upcoming = $this->createEventWithAssessment(
            Carbon::now()->addDays(1),
            Carbon::now()->addDays(5),
            5
        );

        $this->assertEquals('undefined', $running->score);
        $this->assertEquals('undefined', $endedYesterday->score);
        $this->assertEquals('undefined', $endedLongAgo->score);
        $this->assertEquals('undefined', $upcoming->score);

        $this->artisan('report:generate')
            ->expectsOutputToContain('Starting inline report generation')
            ->assertSuccessful();

        $running->refresh();
        $endedYesterday->refresh();
        $endedLongAgo->refresh();
        $upcoming->refresh();

        // Running and ended yesterday should have scores generated
        $this->assertEquals('5', (string) $running->score);
        $this->assertEquals('4', (string) $endedYesterday->score);

        // Ended long ago and upcoming should remain untouched
        $this->assertEquals('undefined', $endedLongAgo->score);
        $this->assertEquals('undefined', $upcoming->score);

        Carbon::setTestNow();
    }

    public function test_report_generate_command_with_all_flag_processes_all_events()
    {
        Carbon::setTestNow('2026-09-08 12:00:00');

        $endedLongAgo = $this->createEventWithAssessment(
            Carbon::now()->subDays(30),
            Carbon::now()->subDays(5),
            3
        );

        $this->assertEquals('undefined', $endedLongAgo->score);

        $this->artisan('report:generate --all')
            ->assertSuccessful();

        $endedLongAgo->refresh();
        $this->assertEquals('3', (string) $endedLongAgo->score);

        Carbon::setTestNow();
    }

    public function test_report_generate_command_with_event_option_processes_specific_event()
    {
        Carbon::setTestNow('2026-09-08 12:00:00');

        $event1 = $this->createEventWithAssessment(
            Carbon::now()->subDays(30),
            Carbon::now()->subDays(5),
            3
        );
        $event2 = $this->createEventWithAssessment(
            Carbon::now()->subDays(30),
            Carbon::now()->subDays(5),
            4
        );

        $this->artisan("report:generate --event={$event1->id}")
            ->assertSuccessful();

        $event1->refresh();
        $event2->refresh();

        $this->assertEquals('3', (string) $event1->score);
        $this->assertEquals('undefined', $event2->score);

        Carbon::setTestNow();
    }

    public function test_report_generate_command_executes_inline_without_queue_worker()
    {
        Carbon::setTestNow('2026-09-08 12:00:00');

        $running = $this->createEventWithAssessment(
            Carbon::now()->subDays(2),
            Carbon::now()->addDays(2),
            5
        );

        $this->assertEquals('undefined', $running->score);

        // Executes purely inline and writes directly to database
        $this->artisan('report:generate')
            ->expectsOutputToContain('Starting inline report generation')
            ->expectsOutputToContain('Report generated inline successfully')
            ->assertSuccessful();

        $running->refresh();
        $this->assertEquals('5', (string) $running->score);

        Carbon::setTestNow();
    }

    public function test_kernel_has_report_generate_scheduled_daily()
    {
        $schedule = app()->make(Schedule::class);
        $events = collect($schedule->events());

        $reportEvent = $events->first(function ($event) {
            return str_contains($event->command, 'report:generate');
        });

        $this->assertNotNull($reportEvent, 'report:generate is not scheduled in Kernel');
        // Daily schedule expression in cron is '0 0 * * *'
        $this->assertEquals('0 0 * * *', $reportEvent->expression);
    }
}
