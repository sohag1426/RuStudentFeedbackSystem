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
     * Blocks creation if any student group in the department has a null or empty session.
     * If a session is specified, returns false if a student group for that session already exists in the department.
     */
    public function create(User $user, ?string $session = null): bool
    {
        if (StudentGroup::hasNullSession($user->department_id)) {
            return false;
        }

        if ($session !== null && trim($session) !== '') {
            return ! StudentGroup::existsForSession($user->department_id, $session);
        }

        return true;
    }

    /**
     * Determine whether the user can create a student group for a given session.
     */
    public function createForSession(User $user, string $session): bool
    {
        return $this->create($user, $session);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, StudentGroup $studentGroup): bool
    {
        return $user->department_id == $studentGroup->department_id;
    }

    /**
     * Determine whether the user can update the session of a student group.
     */
    public function updateSession(User $user, StudentGroup $studentGroup, ?string $newSession = null): bool
    {
        if ($user->department_id != $studentGroup->department_id) {
            return false;
        }

        if ($newSession !== null && trim($newSession) !== '') {
            return ! StudentGroup::existsForSession($user->department_id, $newSession, $studentGroup->id);
        }

        return true;
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
