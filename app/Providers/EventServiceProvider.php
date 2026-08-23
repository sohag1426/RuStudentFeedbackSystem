<?php

namespace App\Providers;

use App\Models\AssessmentEvent;
use App\Models\AssessmentEventStudent;
use App\Models\Course;
use App\Models\StudentGroup;
use App\Models\StudentGroupMember;
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
        Course::observe(CourseObserver::class);
        StudentGroup::observe(StudentGroupObserver::class);
        StudentGroupMember::observe(StudentGroupMemberObserver::class);
        AssessmentEventStudent::observe(AssessmentEventStudentObserver::class);
        AssessmentEvent::observe(AssessmentEventObserver::class);
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
