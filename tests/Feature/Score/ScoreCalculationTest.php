<?php

namespace Tests\Feature\Score;

use App\Models\Assessment;
use App\Models\AssessmentEvent;
use App\Models\AssessmentStatus;
use App\Models\Course;
use App\Models\Department;
use App\Models\DetailedScore;
use App\Models\Question;
use App\Models\QuestionsGroup;
use App\Models\StudentGroup;
use App\Models\User;
use App\Services\ScoreService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScoreCalculationTest extends TestCase
{
    use RefreshDatabase;

    public function test_score_generation_calculates_correct_event_and_detailed_scores()
    {
        $department = Department::create(['en_name' => 'Department of CSE']);
        $teacher = User::factory()->create(['department_id' => $department->id]);
        $course = Course::create([
            'user_id' => $teacher->id,
            'department_id' => $department->id,
            'code' => 'CSE101',
            'name' => 'Structured Programming',
        ]);
        $group = StudentGroup::create([
            'user_id' => $teacher->id,
            'department_id' => $department->id,
            'name' => 'CSE 2023',
            'year' => '1st Year',
            'semester' => '1st Semester',
        ]);
        $event = AssessmentEvent::create([
            'user_id' => $teacher->id,
            'department_id' => $department->id,
            'teacher_id' => $teacher->id,
            'course_id' => $course->id,
            'group_id' => $group->id,
            'start_time' => Carbon::now()->subDays(2),
            'stop_time' => Carbon::now()->addDays(2),
        ]);

        $qGroup = QuestionsGroup::create(['en_name' => 'General Quality', 'bn_name' => 'মান']);
        $q1 = Question::create([
            'department_id' => $department->id,
            'questions_group_id' => $qGroup->id,
            'en' => 'Q1',
            'bn' => 'প্র১',
        ]);
        $q2 = Question::create([
            'department_id' => $department->id,
            'questions_group_id' => $qGroup->id,
            'en' => 'Q2',
            'bn' => 'প্র২',
        ]);

        // Student 1 ratings: Q1 = 5, Q2 = 4
        Assessment::create(['department_id' => $department->id, 'event_id' => $event->id, 'question_id' => $q1->id, 'score' => 5]);
        Assessment::create(['department_id' => $department->id, 'event_id' => $event->id, 'question_id' => $q2->id, 'score' => 4]);
        AssessmentStatus::create(['department_id' => $department->id, 'event_id' => $event->id, 'student_id' => 19100001, 'status' => 1]);

        // Student 2 ratings: Q1 = 4, Q2 = 3
        Assessment::create(['department_id' => $department->id, 'event_id' => $event->id, 'question_id' => $q1->id, 'score' => 4]);
        Assessment::create(['department_id' => $department->id, 'event_id' => $event->id, 'question_id' => $q2->id, 'score' => 3]);
        AssessmentStatus::create(['department_id' => $department->id, 'event_id' => $event->id, 'student_id' => 19100002, 'status' => 1]);

        ScoreService::generateScore($event);

        $event->refresh();

        // Total score = (5+4+4+3) = 16 / 4 = 4.00
        $this->assertEquals(4.0, (float) $event->score);
        $this->assertEquals(2, $event->assessment_count);

        // Detailed scores:
        // Q1 average = (5+4)/2 = 4.5
        // Q2 average = (4+3)/2 = 3.5
        $detailedQ1 = DetailedScore::where('event_id', $event->id)->where('question_id', $q1->id)->first();
        $detailedQ2 = DetailedScore::where('event_id', $event->id)->where('question_id', $q2->id)->first();

        $this->assertNotNull($detailedQ1);
        $this->assertNotNull($detailedQ2);
        $this->assertEquals(4.5, (float) $detailedQ1->score);
        $this->assertEquals(3.5, (float) $detailedQ2->score);
    }

    public function test_score_generation_calculates_and_stores_feedback_percentage()
    {
        $department = Department::create(['en_name' => 'Department of CSE']);
        $teacher = User::factory()->create(['department_id' => $department->id]);
        $course = Course::create([
            'user_id' => $teacher->id,
            'department_id' => $department->id,
            'code' => 'CSE102',
            'name' => 'Algorithms',
        ]);
        $group = StudentGroup::create([
            'user_id' => $teacher->id,
            'department_id' => $department->id,
            'name' => 'CSE 2024',
            'session' => '2026-2027',
            'year' => '2nd Year',
            'semester' => '1st Semester',
        ]);
        $event = AssessmentEvent::create([
            'user_id' => $teacher->id,
            'department_id' => $department->id,
            'teacher_id' => $teacher->id,
            'course_id' => $course->id,
            'group_id' => $group->id,
            'start_time' => Carbon::now()->subDays(2),
            'stop_time' => Carbon::now()->addDays(2),
        ]);

        // 4 total students in event
        for ($i = 1; $i <= 4; $i++) {
            \App\Models\AssessmentEventStudent::create([
                'event_id' => $event->id,
                'department_id' => $department->id,
                'group_id' => $group->id,
                'student_id' => "2010000{$i}",
                'name' => "Student {$i}",
            ]);
        }

        $qGroup = QuestionsGroup::create(['en_name' => 'General Quality 2', 'bn_name' => 'মান ২']);
        $q = Question::create([
            'department_id' => $department->id,
            'questions_group_id' => $qGroup->id,
            'en' => 'Q1',
            'bn' => 'প্র১',
        ]);

        // 3 out of 4 students gave feedback (75%)
        for ($i = 1; $i <= 3; $i++) {
            Assessment::create([
                'department_id' => $department->id,
                'event_id' => $event->id,
                'question_id' => $q->id,
                'score' => 4,
            ]);
            AssessmentStatus::create([
                'department_id' => $department->id,
                'event_id' => $event->id,
                'student_id' => "2010000{$i}",
                'status' => 1,
            ]);
        }

        ScoreService::generateScore($event);

        $event->refresh();
        $this->assertEquals(75.0, (float) $event->feedback_percentage);
        $this->assertDatabaseHas('assessment_events', [
            'id' => $event->id,
            'feedback_percentage' => 75.0,
        ]);
    }
}
