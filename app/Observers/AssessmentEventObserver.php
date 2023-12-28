<?php

namespace App\Observers;

use App\Models\assessment_event;
use App\Models\log;
use Illuminate\Support\Facades\Log as FacadesLog;

class AssessmentEventObserver
{
    /**
     * Handle the assessment_event "created" event.
     */
    public function created(assessment_event $assessment_event): void
    {
        if (auth()->user()) {
            try {
                $log = new log();
                $log->user_id = auth()->user()->id;
                $log->department_id = auth()->user()->department_id;
                $log->topic = 'feedback event created';
                $log->log = 'id : ' . $assessment_event->id;
                $log->model_type = 'App\Models\assessment_event';
                $log->model_id = $assessment_event->id;
                $log->save();
            } catch (\Throwable $th) {
                FacadesLog::error($th->getMessage());
            }
        }
    }

    /**
     * Handle the assessment_event "updated" event.
     */
    public function updated(assessment_event $assessment_event): void
    {
        //
    }

    /**
     * Handle the assessment_event "deleted" event.
     */
    public function deleted(assessment_event $assessment_event): void
    {
        if (auth()->user()) {
            try {
                $log = new log();
                $log->user_id = auth()->user()->id;
                $log->department_id = auth()->user()->department_id;
                $log->topic = 'feedback event deleted';
                $log->log = 'id : ' . $assessment_event->id;
                $log->model_type = 'App\Models\assessment_event';
                $log->model_id = $assessment_event->id;
                $log->save();
            } catch (\Throwable $th) {
                FacadesLog::error($th->getMessage());
            }
        }
    }

    /**
     * Handle the assessment_event "restored" event.
     */
    public function restored(assessment_event $assessment_event): void
    {
        //
    }

    /**
     * Handle the assessment_event "force deleted" event.
     */
    public function forceDeleted(assessment_event $assessment_event): void
    {
        //
    }
}
