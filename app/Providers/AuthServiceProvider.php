<?php

namespace App\Providers;

use App\Models\AssessmentEvent;
use App\Models\AssessmentEventStudent;
use App\Models\AssessmentStatus;
use App\Models\Course;
use App\Models\StudentGroup;
use App\Models\StudentGroupMember;
use App\Models\User;
use App\Policies\AssessmentEventPolicy;
use App\Policies\CoursePolicy;
use App\Policies\StudentGroupMemberPolicy;
use App\Policies\StudentGroupPolicy;
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
        AssessmentEvent::class => AssessmentEventPolicy::class,
        StudentGroupMember::class => StudentGroupMemberPolicy::class,
        Course::class => CoursePolicy::class,
        StudentGroup::class => StudentGroupPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        Gate::define('remove-student-from-feedback-event', function (User $user, AssessmentEvent $assessmentEvent, AssessmentEventStudent $assessmentEventStudent) {
            if (AssessmentStatus::where('event_id', $assessmentEvent->id)->where('student_id', $assessmentEventStudent->student_id)->count()) {
                return false;
            }

            return true;
        });
    }
}
