<?php

namespace App\Observers;

use App\Models\Log;
use App\Models\StudentGroupMember;
use Illuminate\Support\Facades\Log as FacadesLog;

class StudentGroupMemberObserver
{
    /**
     * Handle the StudentGroupMember "created" event.
     */
    public function created(StudentGroupMember $student_group_member): void
    {
        if (auth()->user()) {
            try {
                $log = new Log();
                $log->user_id = auth()->user()->id;
                $log->department_id = auth()->user()->department_id;
                $log->topic = 'student group member created';
                $log->log = 'group_id : ' . $student_group_member->group_id . ' student_id: ' . $student_group_member->student_id;
                $log->model_type = StudentGroupMember::class;
                $log->model_id = $student_group_member->id;
                $log->save();
            } catch (\Throwable $th) {
                FacadesLog::error($th->getMessage());
            }
        }
    }

    /**
     * Handle the StudentGroupMember "updated" event.
     */
    public function updated(StudentGroupMember $student_group_member): void
    {
        //
    }

    /**
     * Handle the StudentGroupMember "deleted" event.
     */
    public function deleted(StudentGroupMember $student_group_member): void
    {
        if (auth()->user()) {
            try {
                $log = new Log();
                $log->user_id = auth()->user()->id;
                $log->department_id = auth()->user()->department_id;
                $log->topic = 'student group member deleted';
                $log->log = 'group_id : ' . $student_group_member->group_id . ' student_id: ' . $student_group_member->student_id;
                $log->model_type = StudentGroupMember::class;
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
