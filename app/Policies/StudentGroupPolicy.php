<?php

namespace App\Policies;

use App\Models\assessment_event;
use App\Models\student_group;
use App\Models\student_group_member;
use App\Models\User;

class StudentGroupPolicy
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
    public function view(User $user, student_group $studentGroup): bool
    {
        return $user->department_id == $studentGroup->department_id;
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
    public function update(User $user, student_group $studentGroup): bool
    {
        return $user->department_id == $studentGroup->department_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, student_group $studentGroup): bool
    {
        // if (student_group_member::where('group_id', $studentGroup->id)->exists()) {
        //     return false;
        // }

        // if (assessment_event::where('department_id', $user->department_id)->where('group_id', $studentGroup->id)->exists()) {
        //     return false;
        // }

        return $user->department_id == $studentGroup->department_id && $user->role == 'DepartmentManager';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, student_group $studentGroup): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, student_group $studentGroup): bool
    {
        return false;
    }
}
