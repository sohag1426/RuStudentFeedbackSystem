<?php

namespace App\Observers;

use App\Models\log;
use App\Models\student_group;
use Illuminate\Support\Facades\Log as FacadesLog;

class StudentGroupObserver
{
    /**
     * Handle the student_group "created" event.
     */
    public function created(student_group $student_group): void
    {
        if (auth()->user()) {
            try {
                $log = new log();
                $log->user_id = auth()->user()->id;
                $log->department_id = auth()->user()->department_id;
                $log->topic = 'student group created';
                $log->log = 'name: ' . $student_group->name;
                $log->model_type = 'App\Models\student_group';
                $log->model_id = $student_group->id;
                $log->save();
            } catch (\Throwable $th) {
                FacadesLog::error($th->getMessage());
            }
        }
    }

    /**
     * Handle the student_group "updated" event.
     */
    public function updated(student_group $student_group): void
    {
        if (auth()->user()) {
            if ($student_group->wasChanged('name')) {
                try {
                    $log = new log();
                    $log->user_id = auth()->user()->id;
                    $log->department_id = auth()->user()->department_id;
                    $log->topic = 'student group name updated';
                    $log->log = 'Original: ' . $student_group->getOriginal('name') . ' New: ' . $student_group->name;
                    $log->model_type = 'App\Models\student_group';
                    $log->model_id = $student_group->id;
                    $log->save();
                } catch (\Throwable $th) {
                    FacadesLog::error($th->getMessage());
                }
            }
        }
    }

    /**
     * Handle the student_group "deleted" event.
     */
    public function deleted(student_group $student_group): void
    {
        //
    }

    /**
     * Handle the student_group "restored" event.
     */
    public function restored(student_group $student_group): void
    {
        //
    }

    /**
     * Handle the student_group "force deleted" event.
     */
    public function forceDeleted(student_group $student_group): void
    {
        //
    }
}
