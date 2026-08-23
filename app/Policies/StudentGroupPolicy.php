<?php

namespace App\Policies;

use App\Models\AssessmentEvent;
use App\Models\StudentGroup;
use App\Models\StudentGroupMember;
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
    public function view(User $user, StudentGroup $studentGroup): bool
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
    public function update(User $user, StudentGroup $studentGroup): bool
    {
        return $user->department_id == $studentGroup->department_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, StudentGroup $studentGroup): bool
    {
        if (StudentGroupMember::where('group_id', $studentGroup->id)->exists()) {
            return false;
        }

        if (AssessmentEvent::where('department_id', $user->department_id)->where('group_id', $studentGroup->id)->exists()) {
            return false;
        }

        return $user->department_id == $studentGroup->department_id && ($user->role == 'DepartmentManager' || $user->role == 'DepartmentChair');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, StudentGroup $studentGroup): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, StudentGroup $studentGroup): bool
    {
        return false;
    }
}
