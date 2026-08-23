<?php

namespace Tests\Feature\Student;

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
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

class StudentAssessmentTest extends TestCase
{
    use RefreshDatabase;

    protected $department;
    protected $teacher;
    protected $course;
    protected $group;
    protected $event;
    protected $student;
    protected $questionGroup;
    protected $question;

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

        $this->event = AssessmentEvent::create([
            'user_id' => $this->teacher->id,
            'department_id' => $this->department->id,
            'teacher_id' => $this->teacher->id,
            'course_id' => $this->course->id,
            'group_id' => $this->group->id,
            'start_time' => Carbon::now()->subHour(),
            'stop_time' => Carbon::now()->addDays(2),
        ]);

        $this->student = AssessmentEventStudent::create([
            'event_id' => $this->event->id,
            'department_id' => $this->department->id,
            'group_id' => $this->group->id,
            'student_id' => '19100001',
            'name' => 'Test Student',
        ]);

        $this->questionGroup = QuestionsGroup::create([
            'en_name' => 'Teaching Quality',
            'bn_name' => 'শিক্ষাদান মান',
        ]);

        $this->question = Question::create([
            'department_id' => $this->department->id,
            'questions_group_id' => $this->questionGroup->id,
            'en' => 'The teacher explains concepts clearly.',
            'bn' => 'শিক্ষক বিষয়টি পরিষ্কারভাবে বুঝিয়ে দেন।',
        ]);
    }

    public function test_student_login_page_can_be_rendered()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_student_without_enrolled_courses_cannot_login()
    {
        $response = $this->post('/student-login', [
            'student_id' => '99999999',
            'password' => 'secret123',
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHas('error', 'There is no course available for you to provide feedback.');
    }

    public function test_student_can_login_with_valid_credentials()
    {
        Http::fake([
            '*' => Http::response(base64_encode(json_encode(['error_code' => 0, 'message' => 'Success'])), 200),
        ]);

        $response = $this->post('/student-login', [
            'student_id' => '19100001',
            'password' => 'student_pass',
        ]);

        $response->assertRedirect(route('assessment_event_students.assessment_events.index', ['assessment_event_student' => $this->student]));
        $this->assertTrue(session()->has('student_auth_token_' . $this->student->id));
    }

    public function test_student_can_view_assessment_form()
    {
        $token = Str::random(40);
        Cache::put('student_token_' . $this->student->id, $token, now()->addMinutes(60));

        $response = $this->withSession(['student_auth_token_' . $this->student->id => $token])
            ->get(route('assessment_event_students.assessment_events.edit', [
                'assessment_event_student' => $this->student,
                'assessment_event' => $this->event,
            ]));

        $response->assertStatus(200);
    }

    public function test_student_can_submit_feedback_successfully()
    {
        $token = Str::random(40);
        Cache::put('student_token_' . $this->student->id, $token, now()->addMinutes(60));

        $response = $this->withSession(['student_auth_token_' . $this->student->id => $token])
            ->put(route('assessment_event_students.assessment_events.update', [
                'assessment_event_student' => $this->student,
                'assessment_event' => $this->event,
            ]), [
                (string) $this->question->id => 5,
                'comment' => 'Excellent course and teacher.',
            ]);

        $response->assertRedirect(route('assessment_event_students.assessment_events.index', ['assessment_event_student' => $this->student]));
        $response->assertSessionHas('info', 'Feedback submitted successfully!');

        $this->assertDatabaseHas('assessments', [
            'event_id' => $this->event->id,
            'question_id' => $this->question->id,
            'score' => 5,
        ]);

        $this->assertDatabaseHas('assessment_statuses', [
            'event_id' => $this->event->id,
            'student_id' => '19100001',
            'status' => 1,
        ]);

        $this->assertDatabaseHas('comments', [
            'event_id' => $this->event->id,
            'comment' => 'Excellent course and teacher.',
        ]);
    }

    public function test_student_cannot_submit_feedback_twice()
    {
        AssessmentStatus::create([
            'department_id' => $this->department->id,
            'event_id' => $this->event->id,
            'student_id' => '19100001',
            'status' => 1,
        ]);

        $token = Str::random(40);
        Cache::put('student_token_' . $this->student->id, $token, now()->addMinutes(60));

        $response = $this->withSession(['student_auth_token_' . $this->student->id => $token])
            ->get(route('assessment_event_students.assessment_events.edit', [
                'assessment_event_student' => $this->student,
                'assessment_event' => $this->event,
            ]));

        $response->assertRedirect(route('assessment_event_students.assessment_events.index', ['assessment_event_student' => $this->student]));
        $response->assertSessionHas('info', 'Feedback was already completed!');
    }

    public function test_student_can_logout()
    {
        $token = Str::random(40);
        Cache::put('student_token_' . $this->student->id, $token, now()->addMinutes(60));

        $response = $this->withSession(['student_auth_token_' . $this->student->id => $token])
            ->post(route('assessment_event_students.logout.store', ['assessment_event_student' => $this->student]));

        $response->assertRedirect('/');
        $this->assertFalse(session()->has('student_auth_token_' . $this->student->id));
        $this->assertNull(Cache::get('student_token_' . $this->student->id));
    }
}
