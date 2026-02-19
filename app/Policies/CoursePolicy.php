<?php

namespace App\Policies;

use App\Models\assessment_event;
use App\Models\course;
use App\Models\User;

// use Illuminate\Auth\Access\Response;

class CoursePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, course $course): bool
    {
        if ($user->department_id == $course->department_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, course $course): bool
    {
        if (assessment_event::where('department_id', $user->department_id)->where('course_id', $course->id)->count()) {
            return false;
        }

        if ($user->department_id == $course->department_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, course $course): bool
    {
        if (assessment_event::where('department_id', $user->department_id)->where('course_id', $course->id)->count()) {
            return false;
        }

        if ($user->department_id == $course->department_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, course $course): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, course $course): bool
    {
        return false;
    }
}
