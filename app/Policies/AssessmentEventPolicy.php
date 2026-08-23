<?php

namespace App\Policies;

use App\Models\Assessment;
use App\Models\AssessmentEvent;
use App\Models\DetailedScore;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Access\HandlesAuthorization;

class AssessmentEventPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function generateReport(User $user, AssessmentEvent $assessmentEvent): bool
    {
        if ($user->department_id !== $assessmentEvent->department_id) {
            return false;
        }

        if ($user->role === 'DepartmentChair' || $user->id === $assessmentEvent->teacher_id || $user->id === $assessmentEvent->user_id) {
            return Assessment::where('event_id', $assessmentEvent->id)->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function downloadReport(User $user, AssessmentEvent $assessmentEvent): bool
    {
        if ($user->department_id !== $assessmentEvent->department_id) {
            return false;
        }

        if ($user->role === 'DepartmentChair' || $user->id === $assessmentEvent->teacher_id || $user->id === $assessmentEvent->user_id) {
            return DetailedScore::where('event_id', $assessmentEvent->id)->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can view any models.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, AssessmentEvent $assessmentEvent)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewScore(User $user, AssessmentEvent $assessmentEvent): bool
    {
        if ($user->department_id !== $assessmentEvent->department_id) {
            return false;
        }

        if ($user->role === 'DepartmentChair' || $user->id === $assessmentEvent->teacher_id || $user->id === $assessmentEvent->user_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, AssessmentEvent $assessmentEvent)
    {
        if ($user->id !== $assessmentEvent->user_id && $user->role !== 'DepartmentChair') {
            return false;
        }

        $now = Carbon::now();
        $extendTime = Carbon::parse($assessmentEvent->stop_time)->addDays(config('app.event_extend_limit', 30));
        if ($now->lessThan($extendTime)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, AssessmentEvent $assessmentEvent)
    {
        if ($user->id !== $assessmentEvent->user_id) {
            return false;
        }

        if ($user->department_id !== $assessmentEvent->department_id) {
            return false;
        }

        if (Assessment::where('event_id', $assessmentEvent->id)->exists()) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, AssessmentEvent $assessmentEvent)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, AssessmentEvent $assessmentEvent)
    {
        //
    }
}
