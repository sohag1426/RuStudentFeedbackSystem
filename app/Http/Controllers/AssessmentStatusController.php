<?php

namespace App\Http\Controllers;

use App\Models\assessment_event;
use App\Models\assessment_event_student;
use App\Models\assessment_status;

class AssessmentStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \App\Models\assessment_event  $assessment_event
     * @return \Illuminate\Http\Response
     */
    public function index(assessment_event $assessment_event)
    {
        $assessment_statuses = assessment_status::where('event_id', $assessment_event->id)->get();

        $students = assessment_event_student::where('event_id', $assessment_event->id)->get();

        $assessed = $students->filter(function ($value, $key) use ($assessment_statuses) {
            return $assessment_statuses->where('student_id', $value->student_id)->count() > 0;
        });

        $not_assessed = $students->filter(function ($value, $key) use ($assessment_statuses) {
            return $assessment_statuses->where('student_id', $value->student_id)->count() == 0;
        });

        return view('teacher.assessment_status', ['assessment_event' => $assessment_event, 'assessed' => $assessed, 'not_assessed' => $not_assessed]);
    }
}
