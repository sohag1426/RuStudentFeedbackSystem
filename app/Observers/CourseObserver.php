<?php

namespace App\Observers;

use App\Models\Course;
use App\Models\Log;
use Illuminate\Support\Facades\Log as FacadesLog;

class CourseObserver
{
    /**
     * Handle the Course "created" event.
     */
    public function created(Course $course): void
    {
        if (auth()->user()) {
            try {
                $log = new Log;
                $log->user_id = auth()->user()->id;
                $log->department_id = auth()->user()->department_id;
                $log->topic = 'course created';
                $log->log = 'Code: '.$course->code.' Name: '.$course->name;
                $log->model_type = Course::class;
                $log->model_id = $course->id;
                $log->save();
            } catch (\Throwable $th) {
                FacadesLog::error($th->getMessage());
            }
        }
    }

    /**
     * Handle the Course "updated" event.
     */
    public function updated(Course $course): void
    {
        if (auth()->user()) {
            if ($course->wasChanged('code')) {
                try {
                    $log = new Log;
                    $log->user_id = auth()->user()->id;
                    $log->department_id = auth()->user()->department_id;
                    $log->topic = 'course code updated';
                    $log->log = 'Original: '.$course->getOriginal('code').' New: '.$course->code;
                    $log->model_type = Course::class;
                    $log->model_id = $course->id;
                    $log->save();
                } catch (\Throwable $th) {
                    FacadesLog::error($th->getMessage());
                }
            }

            if ($course->wasChanged('name')) {
                try {
                    $log = new Log;
                    $log->user_id = auth()->user()->id;
                    $log->department_id = auth()->user()->department_id;
                    $log->topic = 'course name updated';
                    $log->log = 'Original: '.$course->getOriginal('name').' New: '.$course->name;
                    $log->model_type = Course::class;
                    $log->model_id = $course->id;
                    $log->save();
                } catch (\Throwable $th) {
                    FacadesLog::error($th->getMessage());
                }
            }

            if ($course->wasChanged('year')) {
                try {
                    $origYear = $course->getOriginal('year');
                    $origYearVal = $origYear instanceof \App\Enums\Year ? $origYear->value : $origYear;
                    $newYearVal = $course->year instanceof \App\Enums\Year ? $course->year->value : $course->year;

                    $log = new Log;
                    $log->user_id = auth()->user()->id;
                    $log->department_id = auth()->user()->department_id;
                    $log->topic = 'course year updated';
                    $log->log = 'Original: '.$origYearVal.' New: '.$newYearVal;
                    $log->model_type = Course::class;
                    $log->model_id = $course->id;
                    $log->save();
                } catch (\Throwable $th) {
                    FacadesLog::error($th->getMessage());
                }
            }

            if ($course->wasChanged('semester')) {
                try {
                    $origSem = $course->getOriginal('semester');
                    $origSemVal = $origSem instanceof \App\Enums\Semester ? $origSem->value : $origSem;
                    $newSemVal = $course->semester instanceof \App\Enums\Semester ? $course->semester->value : $course->semester;

                    $log = new Log;
                    $log->user_id = auth()->user()->id;
                    $log->department_id = auth()->user()->department_id;
                    $log->topic = 'course semester updated';
                    $log->log = 'Original: '.$origSemVal.' New: '.$newSemVal;
                    $log->model_type = Course::class;
                    $log->model_id = $course->id;
                    $log->save();
                } catch (\Throwable $th) {
                    FacadesLog::error($th->getMessage());
                }
            }
        }
    }

    /**
     * Handle the Course "deleted" event.
     */
    public function deleted(Course $course): void
    {
        if (auth()->user()) {
            try {
                $log = new Log;
                $log->user_id = auth()->user()->id;
                $log->department_id = auth()->user()->department_id;
                $log->topic = 'course deleted';
                $log->log = 'Code: '.$course->code.' Name: '.$course->name;
                $log->model_type = Course::class;
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
