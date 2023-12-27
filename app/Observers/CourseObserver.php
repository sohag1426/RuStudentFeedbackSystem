<?php

namespace App\Observers;

use App\Models\course;
use App\Models\log;
use Illuminate\Support\Facades\Log as FacadesLog;

class CourseObserver
{
    /**
     * Handle the course "created" event.
     */
    public function created(course $course): void
    {
        if (auth()->user()) {
            try {
                $log = new log();
                $log->user_id = auth()->user()->id;
                $log->department_id = auth()->user()->department_id;
                $log->topic = 'course created';
                $log->log = 'Code: ' . $course->code . ' Name: ' . $course->name;
                $log->model_type = 'App\Models\course';
                $log->model_id = $course->id;
                $log->save();
            } catch (\Throwable $th) {
                FacadesLog::error($th->getMessage());
            }
        }
    }

    /**
     * Handle the course "updated" event.
     */
    public function updated(course $course): void
    {
        if (auth()->user()) {
            if ($course->wasChanged('code')) {
                try {
                    $log = new log();
                    $log->user_id = auth()->user()->id;
                    $log->department_id = auth()->user()->department_id;
                    $log->topic = 'course code updated';
                    $log->log = 'Original: ' . $course->getOriginal('code') . ' New: ' . $course->code;
                    $log->model_type = 'App\Models\course';
                    $log->model_id = $course->id;
                    $log->save();
                } catch (\Throwable $th) {
                    FacadesLog::error($th->getMessage());
                }
            }

            if ($course->wasChanged('name')) {
                try {
                    $log = new log();
                    $log->user_id = auth()->user()->id;
                    $log->department_id = auth()->user()->department_id;
                    $log->topic = 'course name updated';
                    $log->log = 'Original: ' . $course->getOriginal('name') . ' New: ' . $course->name;
                    $log->model_type = 'App\Models\course';
                    $log->model_id = $course->id;
                    $log->save();
                } catch (\Throwable $th) {
                    FacadesLog::error($th->getMessage());
                }
            }
        }
    }

    /**
     * Handle the course "deleted" event.
     */
    public function deleted(course $course): void
    {
        if (auth()->user()) {
            try {
                $log = new log();
                $log->user_id = auth()->user()->id;
                $log->department_id = auth()->user()->department_id;
                $log->topic = 'course deleted';
                $log->log = 'Code: ' . $course->code . ' Name: ' . $course->name;
                $log->model_type = 'App\Models\course';
                $log->model_id = $course->id;
                $log->save();
            } catch (\Throwable $th) {
                FacadesLog::error($th->getMessage());
            }
        }
    }

    /**
     * Handle the course "restored" event.
     */
    public function restored(course $course): void
    {
        //
    }

    /**
     * Handle the course "force deleted" event.
     */
    public function forceDeleted(course $course): void
    {
        //
    }
}
