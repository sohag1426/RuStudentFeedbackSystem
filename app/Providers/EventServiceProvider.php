<?php

namespace App\Providers;

use App\Models\assessment_event;
use App\Models\assessment_event_student;
use App\Models\course;
use App\Models\student_group;
use App\Models\student_group_member;
use App\Models\User;
use App\Observers\AssessmentEventObserver;
use App\Observers\AssessmentEventStudentObserver;
use App\Observers\CourseObserver;
use App\Observers\StudentGroupMemberObserver;
use App\Observers\StudentGroupObserver;
use App\Observers\UserObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        User::observe(UserObserver::class);
        course::observe(CourseObserver::class);
        student_group::observe(StudentGroupObserver::class);
        student_group_member::observe(StudentGroupMemberObserver::class);
        assessment_event_student::observe(AssessmentEventStudentObserver::class);
        assessment_event::observe(AssessmentEventObserver::class);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
