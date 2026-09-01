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

class AdminReportTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $department;
    protected $teacher;
    protected $course;
    protected $group;
    protected $event;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::create([
            'name' => 'Super Admin',
            'email' => 'admin@ru.ac.bd',
            'password' => bcrypt('password'),
        ]);

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

        $this->event = AssessmentEvent::create([
            'user_id' => $this->teacher->id,
            'department_id' => $this->department->id,
            'teacher_id' => $this->teacher->id,
            'course_id' => $this->course->id,
            'group_id' => $this->group->id,
            'session' => '2026-2027',
            'year' => '1st Year',
            'semester' => '1st Semester',
            'start_time' => Carbon::now()->subDays(2),
            'stop_time' => Carbon::now()->addDays(2),
            'score' => 4.5,
        ]);
    }

    public function test_admin_can_view_department_reports()
    {
        $response = $this->actingAs($this->admin, 'admin')->get(route('admin-reports.by-department'));
        $response->assertStatus(200);
        $response->assertSee('Computer Science & Engineering');
    }

    public function test_admin_can_view_teacher_reports()
    {
        $response = $this->actingAs($this->admin, 'admin')->get(route('admin-reports.by-teacher'));
        $response->assertStatus(200);
        $response->assertSee($this->teacher->name);
    }

    public function test_admin_can_download_department_pdf_report()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin-reports.department.download', ['department' => $this->department]));

        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
    }

    public function test_admin_can_download_teacher_pdf_report()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin-reports.teacher.download', ['teacher' => $this->teacher]));

        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
    }

    public function test_department_report_view_includes_session_year_semester_and_feedback_percentage()
    {
        $this->event->feedback_percentage = 85.5;
        $this->event->save();

        $view = $this->view('admin.reports.pdf_department', [
            'department' => $this->department,
            'events' => collect([$this->event]),
        ]);

        $view->assertSee('<th>Session</th>', false);
        $view->assertSee('<th>Year</th>', false);
        $view->assertSee('<th>Semester</th>', false);
        $view->assertSee('<th>%Feedback</th>', false);
        $view->assertSee('2026-2027');
        $view->assertSee('1st Year');
        $view->assertSee('1st Semester');
        $view->assertSee('85.5%');
    }

    public function test_teacher_report_view_includes_session_year_semester_and_feedback_percentage()
    {
        $this->event->feedback_percentage = 92.0;
        $this->event->save();

        $view = $this->view('admin.reports.pdf_teacher', [
            'teacher' => $this->teacher,
            'events' => collect([$this->event]),
        ]);

        $view->assertSee('<th>Session</th>', false);
        $view->assertSee('<th>Year</th>', false);
        $view->assertSee('<th>Semester</th>', false);
        $view->assertSee('<th>%Feedback</th>', false);
        $view->assertSee('2026-2027');
        $view->assertSee('1st Year');
        $view->assertSee('1st Semester');
        $view->assertSee('92%');
    }
}
