<?php

namespace App\Providers;

use App\Models\assessment_event;
use App\Models\assessment_event_student;
use App\Models\assessment_status;
use App\Models\course;
use App\Models\student_group_member;
use App\Models\User;
use App\Policies\AssessmentEventPolicy;
use App\Policies\CoursePolicy;
use App\Policies\StudentGroupMemberPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        assessment_event::class => AssessmentEventPolicy::class,
        student_group_member::class => StudentGroupMemberPolicy::class,
        course::class => CoursePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        Gate::define('remove-student-from-feedback-event', function (User $user, assessment_event $assessment_event, assessment_event_student $assessment_event_student) {
            if (assessment_status::where('event_id', $assessment_event->id)->where('student_id', $assessment_event_student->student_id)->count()) {
                return false;
            }
            return true;
        });
    }
}
