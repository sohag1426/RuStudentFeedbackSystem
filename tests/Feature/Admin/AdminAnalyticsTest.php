<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\AssessmentEvent;
use App\Models\Course;
use App\Models\Department;
use App\Models\StudentGroup;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $department1;
    protected $department2;
    protected $teacher;
    protected $course;
    protected $group1;
    protected $group2;
    protected $event1;
    protected $event2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::create([
            'name' => 'Super Admin',
            'email' => 'admin@ru.ac.bd',
            'password' => bcrypt('password'),
        ]);

        $this->department1 = Department::create(['en_name' => 'Computer Science & Engineering']);
        $this->department2 = Department::create(['en_name' => 'Electrical & Electronic Engineering']);

        $this->teacher = User::factory()->create([
            'department_id' => $this->department1->id,
            'role' => 'teacher',
        ]);

        $this->course = Course::create([
            'user_id' => $this->teacher->id,
            'department_id' => $this->department1->id,
            'code' => 'CSE101',
            'name' => 'Structured Programming',
        ]);

        $this->group1 = StudentGroup::create([
            'user_id' => $this->teacher->id,
            'department_id' => $this->department1->id,
            'name' => 'CSE 2026',
            'session' => '2026-2027',
            'year' => '1st Year',
            'semester' => '1st Semester',
        ]);

        $this->group2 = StudentGroup::create([
            'user_id' => $this->teacher->id,
            'department_id' => $this->department2->id,
            'name' => 'EEE 2025',
            'session' => '2025-2026',
            'year' => '2nd Year',
            'semester' => '2nd Semester',
        ]);

        $this->event1 = AssessmentEvent::create([
            'user_id' => $this->teacher->id,
            'department_id' => $this->department1->id,
            'teacher_id' => $this->teacher->id,
            'course_id' => $this->course->id,
            'group_id' => $this->group1->id,
            'session' => '2026-2027',
            'year' => '1st Year',
            'semester' => '1st Semester',
            'start_time' => Carbon::now()->subDays(10),
            'stop_time' => Carbon::now()->subDays(5),
            'score' => 4.5,
            'feedback_percentage' => 85.0,
        ]);

        $this->event2 = AssessmentEvent::create([
            'user_id' => $this->teacher->id,
            'department_id' => $this->department2->id,
            'teacher_id' => $this->teacher->id,
            'course_id' => $this->course->id,
            'group_id' => $this->group2->id,
            'session' => '2025-2026',
            'year' => '2nd Year',
            'semester' => '2nd Semester',
            'start_time' => Carbon::now()->subDays(2),
            'stop_time' => Carbon::now()->addDays(5),
            'score' => 3.2,
            'feedback_percentage' => 40.0,
        ]);
    }

    public function test_guest_cannot_access_analytics()
    {
        $response = $this->get(route('admin-analytics.index'));
        $response->assertRedirect(route('admin-login'));
    }

    public function test_admin_can_view_analytics_page_without_filter()
    {
        $response = $this->actingAs($this->admin, 'admin')->get(route('admin-analytics.index'));
        $response->assertStatus(200);
        $response->assertSee('Analytics');
        $response->assertSee('Filter Assessment Events');
        $response->assertDontSee('Download PDF');
        $response->assertDontSee('Results (Total:');
        $response->assertViewHas('hasFilters', false);
        $response->assertViewHas('events', null);
    }

    public function test_admin_sees_results_and_download_button_with_filter()
    {
        $response = $this->actingAs($this->admin, 'admin')->get(route('admin-analytics.index', [
            'session' => '2026-2027',
        ]));
        $response->assertStatus(200);
        $response->assertSee('Results (Total:');
        $response->assertSee('Download PDF');
        $response->assertViewHas('hasFilters', true);
    }

    public function test_admin_can_filter_by_department()
    {
        $response = $this->actingAs($this->admin, 'admin')->get(route('admin-analytics.index', [
            'department_id' => $this->department1->id,
        ]));

        $response->assertStatus(200);
        $response->assertViewHas('events', function ($events) {
            return $events->contains($this->event1) && !$events->contains($this->event2);
        });
    }

    public function test_admin_can_filter_by_session()
    {
        $response = $this->actingAs($this->admin, 'admin')->get(route('admin-analytics.index', [
            'session' => '2026-2027',
        ]));

        $response->assertStatus(200);
        $response->assertViewHas('events', function ($events) {
            return $events->contains($this->event1) && !$events->contains($this->event2);
        });
    }

    public function test_admin_can_filter_by_year_and_semester()
    {
        $response = $this->actingAs($this->admin, 'admin')->get(route('admin-analytics.index', [
            'year' => '2nd Year',
            'semester' => '2nd Semester',
        ]));

        $response->assertStatus(200);
        $response->assertViewHas('events', function ($events) {
            return $events->contains($this->event2) && !$events->contains($this->event1);
        });
    }

    public function test_admin_can_filter_by_start_and_stop_time()
    {
        $response = $this->actingAs($this->admin, 'admin')->get(route('admin-analytics.index', [
            'start_time' => Carbon::now()->subDays(4)->format('Y-m-d'),
        ]));

        $response->assertStatus(200);
        $response->assertViewHas('events', function ($events) {
            return $events->contains($this->event2) && !$events->contains($this->event1);
        });
    }

    public function test_admin_can_filter_by_score_operators()
    {
        // Greater than 4.0
        $responseGt = $this->actingAs($this->admin, 'admin')->get(route('admin-analytics.index', [
            'score_operator' => '>',
            'score' => 4.0,
        ]));
        $responseGt->assertStatus(200);
        $responseGt->assertViewHas('events', function ($events) {
            return $events->contains($this->event1) && !$events->contains($this->event2);
        });

        // Less than 4.0
        $responseLt = $this->actingAs($this->admin, 'admin')->get(route('admin-analytics.index', [
            'score_operator' => '<',
            'score' => 4.0,
        ]));
        $responseLt->assertStatus(200);
        $responseLt->assertViewHas('events', function ($events) {
            return $events->contains($this->event2) && !$events->contains($this->event1);
        });
    }

    public function test_admin_can_filter_by_feedback_percentage_operators()
    {
        // Greater than 50%
        $responseGt = $this->actingAs($this->admin, 'admin')->get(route('admin-analytics.index', [
            'feedback_percentage_operator' => '>',
            'feedback_percentage' => 50,
        ]));
        $responseGt->assertStatus(200);
        $responseGt->assertViewHas('events', function ($events) {
            return $events->contains($this->event1) && !$events->contains($this->event2);
        });

        // Equal to 40%
        $responseEq = $this->actingAs($this->admin, 'admin')->get(route('admin-analytics.index', [
            'feedback_percentage_operator' => '=',
            'feedback_percentage' => 40,
        ]));
        $responseEq->assertStatus(200);
        $responseEq->assertViewHas('events', function ($events) {
            return $events->contains($this->event2) && !$events->contains($this->event1);
        });
    }

    public function test_admin_can_download_analytics_pdf()
    {
        $response = $this->actingAs($this->admin, 'admin')->get(route('admin-analytics.download', [
            'department_id' => $this->department1->id,
            'session' => '2026-2027',
            'score_operator' => '>',
            'score' => 4.0,
        ]));

        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
    }

    public function test_cannot_download_analytics_without_filter()
    {
        $response = $this->actingAs($this->admin, 'admin')->get(route('admin-analytics.download'));
        $response->assertRedirect(route('admin-analytics.index'));
        $response->assertSessionHas('error');
    }
}
