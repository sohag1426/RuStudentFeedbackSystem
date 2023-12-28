<?php

namespace App\Observers;

use App\Models\assessment_event_student;
use App\Models\log;
use Illuminate\Support\Facades\Log as FacadesLog;

class AssessmentEventStudentObserver
{
    /**
     * Handle the assessment_event_student "created" event.
     */
    public function created(assessment_event_student $assessment_event_student): void
    {
        //
    }

    /**
     * Handle the assessment_event_student "updated" event.
     */
    public function updated(assessment_event_student $assessment_event_student): void
    {
        //
    }

    /**
     * Handle the assessment_event_student "deleted" event.
     */
    public function deleted(assessment_event_student $assessment_event_student): void
    {
        if (auth()->user()) {
            try {
                $log = new log();
                $log->user_id = auth()->user()->id;
                $log->department_id = auth()->user()->department_id;
                $log->topic = 'student deleted from feedback event';
                $log->log = 'event_id : ' . $assessment_event_student->event_id . ' student_id: ' . $assessment_event_student->student_id;
                $log->model_type = 'App\Models\student_group_member';
                $log->model_id = $assessment_event_student->id;
                $log->save();
            } catch (\Throwable $th) {
                FacadesLog::error($th->getMessage());
            }
        }
    }

    /**
     * Handle the assessment_event_student "restored" event.
     */
    public function restored(assessment_event_student $assessment_event_student): void
    {
        //
    }

    /**
     * Handle the assessment_event_student "force deleted" event.
     */
    public function forceDeleted(assessment_event_student $assessment_event_student): void
    {
        //
    }
}
