<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\AssessmentEvent;
use App\Models\AssessmentEventStudent;
use App\Models\AssessmentStatus;
use App\Models\Comment;
use App\Models\Question;
use App\Models\QuestionsGroup;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AssessmentController extends Controller
{
    /**
     * Validate that the student has a valid active session and matching cache token.
     */
    private function validateStudentSession(Request $request, AssessmentEventStudent $assessment_event_student): bool
    {
        $sessionToken = $request->session()->get('student_auth_token_' . $assessment_event_student->id);
        $cachedToken = Cache::get('student_token_' . $assessment_event_student->id);

        if (! $sessionToken || ! $cachedToken || ! hash_equals((string) $cachedToken, (string) $sessionToken)) {
            return false;
        }

        return true;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AssessmentEventStudent  $assessment_event_student
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, AssessmentEventStudent $assessment_event_student)
    {
        if (! $this->validateStudentSession($request, $assessment_event_student)) {
            return redirect()->route('student-login-form')->with('info', 'Session expired or invalid token. Please log in again.');
        }

        $assessable_events = AssessmentEventController::getFeedbackEvents($assessment_event_student);

        return view('student.events', ['assessable_events' => $assessable_events, 'assessment_event_student' => $assessment_event_student]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AssessmentEventStudent  $assessment_event_student
     * @param  \App\Models\AssessmentEvent  $assessment_event
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, AssessmentEventStudent $assessment_event_student, AssessmentEvent $assessment_event)
    {
        if (! $this->validateStudentSession($request, $assessment_event_student)) {
            return redirect()->route('student-login-form')->with('info', 'Session expired or invalid token. Please log in again.');
        }

        if (AssessmentStatus::where('event_id', $assessment_event->id)->where('student_id', $assessment_event_student->student_id)->exists()) {
            return redirect()->route('assessment_event_students.assessment_events.index', ['assessment_event_student' => $assessment_event_student])->with('info', 'Feedback was already completed!');
        }

        $startTime = Carbon::parse($assessment_event->start_time);
        if (Carbon::now()->lessThan($startTime)) {
            return redirect()->route('assessment_event_students.assessment_events.index', ['assessment_event_student' => $assessment_event_student])->with('info', 'Please wait until ' . $assessment_event->start_time);
        }

        $highest_score = config('app.highest_score', 5);

        $questions = Question::all();
        $questions_groups = QuestionsGroup::all();

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
     * @param  \App\Models\AssessmentEventStudent  $assessment_event_student
     * @param  \App\Models\AssessmentEvent  $assessment_event
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AssessmentEventStudent $assessment_event_student, AssessmentEvent $assessment_event)
    {
        if (! $this->validateStudentSession($request, $assessment_event_student)) {
            return redirect()->route('student-login-form')->with('info', 'Session expired or invalid token. Please log in again.');
        }

        if (AssessmentStatus::where('event_id', $assessment_event->id)->where('student_id', $assessment_event_student->student_id)->exists()) {
            return redirect()->route('assessment_event_students.assessment_events.index', ['assessment_event_student' => $assessment_event_student])->with('info', 'Feedback was already completed!');
        }

        $questions = Question::all();
        $highest_score = (int) config('app.highest_score', 5);

        DB::transaction(function () use ($request, $assessment_event_student, $assessment_event, $questions, $highest_score) {
            foreach ($questions as $question) {
                $question_id = (string) $question->id;
                if ($request->filled($question_id)) {
                    $scoreVal = (int) $request->input($question_id);
                    if ($scoreVal >= 1 && $scoreVal <= $highest_score) {
                        $assessment = new Assessment();
                        $assessment->department_id = $assessment_event->department_id;
                        $assessment->event_id = $assessment_event->id;
                        $assessment->question_id = $question->id;
                        $assessment->score = $scoreVal;
                        $assessment->save();
                    }
                }
            }

            if ($request->filled('comment')) {
                $comment = new Comment();
                $comment->department_id = $assessment_event->department_id;
                $comment->event_id = $assessment_event->id;
                $comment->comment = (string) $request->input('comment');
                $comment->save();
            }

            $assessment_status = new AssessmentStatus();
            $assessment_status->department_id = $assessment_event->department_id;
            $assessment_status->event_id = $assessment_event->id;
            $assessment_status->student_id = $assessment_event_student->student_id;
            $assessment_status->status = 1;
            $assessment_status->save();
        });

        return redirect()->route('assessment_event_students.assessment_events.index', ['assessment_event_student' => $assessment_event_student])->with('info', 'Feedback submitted successfully!');
    }
}
