<?php

namespace App\Http\Controllers;

use App\Models\AssessmentEvent;
use App\Models\AssessmentEventStudent;
use App\Models\AssessmentStatus;

class AssessmentStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(AssessmentEvent $assessment_event)
    {
        $assessment_statuses = AssessmentStatus::where('event_id', $assessment_event->id)->get();

        $students = AssessmentEventStudent::where('event_id', $assessment_event->id)->get();

        $assessed = $students->filter(function ($value, $key) use ($assessment_statuses) {
            return $assessment_statuses->where('student_id', $value->student_id)->count() > 0;
        });

        $not_assessed = $students->filter(function ($value, $key) use ($assessment_statuses) {
            return $assessment_statuses->where('student_id', $value->student_id)->count() == 0;
        });

        return view('teacher.assessment_status', ['assessment_event' => $assessment_event, 'assessed' => $assessed, 'not_assessed' => $not_assessed]);
    }
}
