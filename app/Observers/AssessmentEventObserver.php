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
        // log
        $logMessages = [];

        if ($assessment_event->wasChanged('teacher_id')) {
            $logMessages[] = 'teacher id was changed from ' . $assessment_event->getOriginal('teacher_id') . ' to ' . $assessment_event->teacher_id;
        }
        if ($assessment_event->wasChanged('course_id')) {
            $logMessages[] = 'course id was changed from ' . $assessment_event->getOriginal('course_id') . ' to ' . $assessment_event->course_id;
        }

        if ($assessment_event->wasChanged('group_id')) {
            $logMessages = 'studen group id was changed from ' . $assessment_event->getOriginal('group_id') . ' to ' . $assessment_event->group_id;
        }

        if ($assessment_event->wasChanged('stop_time')) {
            $logMessages = 'stop_time was changed from ' . $assessment_event->getOriginal('stop_time') . ' to ' . $assessment_event->stop_time;
        }

        foreach ($logMessages as $logMessage) {
            try {
                $log = new log();
                $log->user_id = auth()->user()->id;
                $log->department_id = auth()->user()->department_id;
                $log->topic = 'feedback event updated';
                $log->log = $logMessage;
                $log->model_type = 'App\Models\assessment_event';
                $log->model_id = $assessment_event->id;
                $log->save();
            } catch (\Throwable $th) {
                FacadesLog::error($th->getMessage());
            }
        }
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
