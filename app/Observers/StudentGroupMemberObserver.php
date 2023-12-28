<?php

namespace App\Observers;

use App\Models\log;
use App\Models\student_group_member;
use Illuminate\Support\Facades\Log as FacadesLog;

class StudentGroupMemberObserver
{
    /**
     * Handle the student_group_member "created" event.
     */
    public function created(student_group_member $student_group_member): void
    {
        if (auth()->user()) {
            try {
                $log = new log();
                $log->user_id = auth()->user()->id;
                $log->department_id = auth()->user()->department_id;
                $log->topic = 'student group member created';
                $log->log = 'group_id : ' . $student_group_member->group_id . ' student_id: ' . $student_group_member->student_id;
                $log->model_type = 'App\Models\student_group_member';
                $log->model_id = $student_group_member->id;
                $log->save();
            } catch (\Throwable $th) {
                FacadesLog::error($th->getMessage());
            }
        }
    }

    /**
     * Handle the student_group_member "updated" event.
     */
    public function updated(student_group_member $student_group_member): void
    {
        //
    }

    /**
     * Handle the student_group_member "deleted" event.
     */
    public function deleted(student_group_member $student_group_member): void
    {
        if (auth()->user()) {
            try {
                $log = new log();
                $log->user_id = auth()->user()->id;
                $log->department_id = auth()->user()->department_id;
                $log->topic = 'student group member deleted';
                $log->log = 'group_id : ' . $student_group_member->group_id . ' student_id: ' . $student_group_member->student_id;
                $log->model_type = 'App\Models\student_group_member';
                $log->model_id = $student_group_member->id;
                $log->save();
            } catch (\Throwable $th) {
                FacadesLog::error($th->getMessage());
            }
        }
    }

    /**
     * Handle the student_group_member "restored" event.
     */
    public function restored(student_group_member $student_group_member): void
    {
        //
    }

    /**
     * Handle the student_group_member "force deleted" event.
     */
    public function forceDeleted(student_group_member $student_group_member): void
    {
        //
    }
}
