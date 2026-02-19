<?php

namespace App\Policies;

use App\Models\assessment;
use App\Models\assessment_event;
use App\Models\detailed_score;
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
    public function generateReport(User $user, assessment_event $assessmentEvent)
    {
        if ($user->department_id !== $assessmentEvent->department_id) {
            return false;
        }

        if ($user->id !== $assessmentEvent->teacher_id) {
            return false;
        }

        if (assessment::where('event_id', $assessmentEvent->id)->count() > 1) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function downloadReport(User $user, assessment_event $assessmentEvent)
    {
        if ($user->department_id !== $assessmentEvent->department_id) {
            return false;
        }

        if ($user->id !== $assessmentEvent->teacher_id) {
            return false;
        }

        if (detailed_score::where('event_id', $assessmentEvent->id)->count()) {
            return true;
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
    public function view(User $user, assessment_event $assessmentEvent)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewScore(User $user, assessment_event $assessmentEvent)
    {
        if ($user->id == $assessmentEvent->teacher_id) {
            return true;
        }

        if ($user->role == 'DepartmentChair' && $user->department_id == $assessmentEvent->department_id) {
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
    public function update(User $user, assessment_event $assessmentEvent)
    {
        if ($user->id !== $assessmentEvent->user_id) {
            return false;
        }

        $now = Carbon::now();
        $extendTime = Carbon::createFromFormat(config('datetimeformat.date_time_format'), $assessmentEvent->stop_time)->addDays(config('app.event_extend_limit'));
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
    public function delete(User $user, assessment_event $assessmentEvent)
    {
        if ($user->id !== $assessmentEvent->user_id) {
            return false;
        }

        if ($user->department_id !== $assessmentEvent->department_id) {
            return false;
        }

        if (assessment::where('event_id', $assessmentEvent->id)->count()) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, assessment_event $assessmentEvent)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, assessment_event $assessmentEvent)
    {
        //
    }
}
