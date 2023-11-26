<?php

namespace App\Http\Controllers;

use App\Models\assessment;
use App\Models\assessment_event;
use App\Models\assessment_event_student;
use App\Models\assessment_status;
use App\Models\comment;
use App\Models\question;
use App\Models\questions_group;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AssessmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \App\Models\assessment_event_student  $assessment_event_student
     * @return \Illuminate\Http\Response
     */
    public function index(assessment_event_student $assessment_event_student)
    {
        $token = Cache::get($assessment_event_student->id, false);
        if (!$token) {
            return redirect()->route('student-login-form')->with('info', 'Invalid Token');
        }

        $assessable_events = AssessmentEventController::getFeedbackEvents($assessment_event_student);

        return view('student.events', ['assessable_events' => $assessable_events, 'assessment_event_student' => $assessment_event_student]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\assessment_event_student  $assessment_event_student
     * @param  \App\Models\assessment_event  $assessment_event
     * @return \Illuminate\Http\Response
     */
    public function edit(assessment_event_student $assessment_event_student, assessment_event $assessment_event)
    {
        $token = Cache::get($assessment_event_student->id, false);
        if (!$token) {
            return redirect()->route('student-login-form')->with('info', 'Invalid Token');
        }

        if (assessment_status::where('event_id', $assessment_event->id)->where('student_id', $assessment_event_student->student_id)->count() != 0) {
            return redirect()->route('assessment_event_students.assessment_events.index', ['assessment_event_student' => $assessment_event_student])->with('info', 'feedback was completed!');
        }

        if (Carbon::now()->lessThan(Carbon::createFromFormat(config('datetimeformat.date_time_format'), $assessment_event->start_time))) {
            return redirect()->route('assessment_event_students.assessment_events.index', ['assessment_event_student' => $assessment_event_student])->with('info', 'Wait untill ' . $assessment_event->start_time);
        }

        $highest_score = config('app.highest_score');

        $questions = question::all();
        $questions_groups = questions_group::all();

        return view('student.assessment_form', [
            'assessment_event' => $assessment_event,
            'assessment_event_student' => $assessment_event_student,
            'questions' => $questions,
            'highest_score' => $highest_score,
            'questions_groups' => $questions_groups,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\assessment_event_student  $assessment_event_student
     * @param  \App\Models\assessment_event  $assessment_event
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, assessment_event_student $assessment_event_student, assessment_event $assessment_event)
    {
        $token = Cache::get($assessment_event_student->id, false);
        if (!$token) {
            return redirect()->route('student-login-form')->with('info', 'Invalid Token');
        }

        if (assessment_status::where('event_id', $assessment_event->id)->where('student_id', $assessment_event_student->student_id)->count()) {
            return redirect()->route('student-login-form');
        }

        // $questions = question::where('department_id', $assessment_event->department_id)->get();
        $questions = question::all();

        foreach ($questions as $question) {
            $question_id = $question->id;
            if ($request->filled($question_id)) {
                $assessment = new assessment();
                $assessment->department_id = $assessment_event->department_id;
                $assessment->event_id = $assessment_event->id;
                $assessment->question_id = $question->id;
                $assessment->score = $request->$question_id;
                $assessment->save();
            }
        }

        if ($request->filled('comment')) {
            $comment = new comment();
            $comment->department_id = $assessment_event->department_id;
            $comment->event_id = $assessment_event->id;
            $comment->comment = $request->comment;
            $comment->save();
        }

        $assessment_status = new assessment_status();
        $assessment_status->department_id = $assessment_event->department_id;
        $assessment_status->event_id = $assessment_event->id;
        $assessment_status->student_id = $assessment_event_student->student_id;
        $assessment_status->status = 1;
        $assessment_status->save();

        return redirect()->route('assessment_event_students.assessment_events.index', ['assessment_event_student' => $assessment_event_student])->with('info', 'feedback submitted successfully!');
    }
}
