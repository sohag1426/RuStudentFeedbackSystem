<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\assessment_event;
use App\Models\student_group_member;
use App\Policies\AssessmentEventPolicy;
use App\Policies\StudentGroupMemberPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

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
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
