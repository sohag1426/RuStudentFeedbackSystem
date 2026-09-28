<?php

namespace App\Http\Controllers;

use App\Models\AssessmentEvent;
use App\Models\AssessmentEventStudent;
use App\Models\AssessmentStatus;
use App\Models\Course;
use App\Models\StudentGroup;
use App\Models\StudentGroupMember;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class AssessmentEventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $request->validate([
            'teacher_id' => 'nullable|exists:users,id',
            'course_id' => 'nullable|exists:courses,id',
        ]);

        $filter = [];

        // default filter
        $filter['department_id'] = $request->user()->department_id;

        // filter by teacher_id
        if ($request->filled('teacher_id')) {
            $filter['teacher_id'] = $request->teacher_id;
        }

        // filter by course
        if ($request->filled('course_id')) {
            $filter['course_id'] = $request->course_id;
        }

        $perPage = (int) $request->input('per_page', 20);

        $assessmentEvents = AssessmentEvent::with(['teacher', 'course', 'group'])
            ->where($filter)
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        $users = User::where('department_id', $request->user()->department_id)
            ->where(function ($query) {
                $query->where('role', 'DepartmentChair')
                    ->orWhere('role', 'teacher');
            })->get();

        $courses = Course::where('department_id', $request->user()->department_id)->get();

        return view('teacher.assessment_events', [
            'assessmentEvents' => $assessmentEvents,
            'assessment_events' => $assessmentEvents,
            'users' => $users,
            'courses' => $courses,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $teachers = User::where('department_id', $request->user()->department_id)
            ->where(function ($query) {
                $query->where('role', 'DepartmentChair')
                    ->orWhere('role', 'teacher');
            })->get();

        $courses = Course::where('department_id', $request->user()->department_id)->get();
        $groups = StudentGroup::where('department_id', $request->user()->department_id)
            ->eligibleForAssessment()
            ->get();

        return view('teacher.assessment_events_create', [
            'teachers' => $teachers,
            'courses' => $courses,
            'groups' => $groups,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'teacher_id' => ['required', 'numeric'],
            'course_id' => ['required', 'numeric'],
            'group_id' => [
                'required',
                'numeric',
                function ($attribute, $value, $fail) use ($request) {
                    $group = StudentGroup::where('id', $value)
                        ->where('department_id', $request->user()->department_id)
                        ->first();

                    if (! $group || ! $group->isEligibleForAssessment()) {
                        $fail('The selected student group is invalid or not eligible for assessment. A valid session, year, semester, and at least one student are required.');
                    }
                },
            ],
            'start_date' => 'required|string',
            'start_hour' => ['required', 'numeric'],
            'start_minute' => ['required', 'numeric'],
            'stop_date' => 'required|string',
            'stop_hour' => ['required', 'numeric'],
            'stop_minute' => ['required', 'numeric'],
        ]);

        $group = StudentGroup::where('id', $request->group_id)
            ->where('department_id', $request->user()->department_id)
            ->first();

        if (! $group || ! $group->isEligibleForAssessment()) {
            return redirect()->route('assessment_events.create')
                ->withInput()
                ->with('info', 'The selected student group is invalid or has no students.')
                ->withErrors(['group_id' => 'The selected student group is invalid or not eligible for assessment. A valid session, year, semester, and at least one student are required.']);
        }

        $now = Carbon::now('Asia/Dhaka')->setHour(0)->setMinute(0);

        $startDate = date_format(date_create($request->start_date), config('datetimeformat.date_format'));
        $stopDate = date_format(date_create($request->stop_date), config('datetimeformat.date_format'));

        $startTime = Carbon::createFromFormat(config('datetimeformat.date_format'), $startDate);
        $startTime->setHour($request->start_hour)->setMinute($request->start_minute);
        if ($startTime->lessThan($now)) {
            return redirect()->route('assessment_events.create')->with('info', 'Backdated events are not possible to create.');
        }

        $stopTime = Carbon::createFromFormat(config('datetimeformat.date_format'), $stopDate);
        $stopTime->setHour($request->stop_hour)->setMinute($request->stop_minute);
        if ($stopTime->lessThan($startTime)) {
            return redirect()->route('assessment_events.create')->with('info', 'It is not possible to stop before the start time.');
        }

        $assessmentEvent = new AssessmentEvent;
        $assessmentEvent->user_id = $request->user()->id;
        $assessmentEvent->department_id = $request->user()->department_id;
        $assessmentEvent->teacher_id = $request->teacher_id;
        $assessmentEvent->course_id = $request->course_id;
        $assessmentEvent->group_id = $request->group_id;
        $assessmentEvent->session = $group->session;
        $assessmentEvent->year = $group->year;
        $assessmentEvent->semester = $group->semester;
        $assessmentEvent->start_time = $startTime;
        $assessmentEvent->stop_time = $stopTime;
        $assessmentEvent->save();

        // assessment_event_students
        $studentGroupMembers = StudentGroupMember::where('group_id', $assessmentEvent->group_id)->get();
        foreach ($studentGroupMembers as $student) {
            $assessmentEventStudent = new AssessmentEventStudent;
            $assessmentEventStudent->event_id = $assessmentEvent->id;
            $assessmentEventStudent->department_id = $student->department_id;
            $assessmentEventStudent->group_id = $student->group_id;
            $assessmentEventStudent->student_id = $student->student_id;
            $assessmentEventStudent->name = $student->name;
            $assessmentEventStudent->save();
        }

        return redirect()->route('assessment_events.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(AssessmentEvent $assessmentEvent)
    {
        return view('teacher.assessment_events_edit', [
            'assessmentEvent' => $assessmentEvent,
            'assessment_event' => $assessmentEvent,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AssessmentEvent $assessmentEvent)
    {
        $request->validate([
            'start_date' => 'required|string',
            'start_hour' => ['required', 'numeric'],
            'start_minute' => ['required', 'numeric'],
            'stop_date' => 'required|string',
            'stop_hour' => ['required', 'numeric'],
            'stop_minute' => ['required', 'numeric'],
        ]);

        $now = Carbon::now('Asia/Dhaka')->setHour(0)->setMinute(0);

        $startDate = date_format(date_create($request->start_date), config('datetimeformat.date_format'));
        $stopDate = date_format(date_create($request->stop_date), config('datetimeformat.date_format'));

        $startTime = Carbon::createFromFormat(config('datetimeformat.date_format'), $startDate);
        $startTime->setHour($request->start_hour)->setMinute($request->start_minute);
        if ($startTime->lessThan($now)) {
            return redirect()->route('assessment_events.edit', ['assessment_event' => $assessmentEvent])->with('info', 'Backdated events are not possible to create.');
        }

        $stopTime = Carbon::createFromFormat(config('datetimeformat.date_format'), $stopDate);
        $stopTime->setHour($request->stop_hour)->setMinute($request->stop_minute);
        if ($stopTime->lessThan($startTime)) {
            return redirect()->route('assessment_events.edit', ['assessment_event' => $assessmentEvent])->with('info', 'It is not possible to stop before the start time.');
        }

        $assessmentEvent->start_time = $startTime;
        $assessmentEvent->stop_time = $stopTime;
        $assessmentEvent->save();

        return redirect()->route('assessment_events.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(AssessmentEvent $assessmentEvent)
    {
        $this->authorize('delete', $assessmentEvent);
        $assessmentEvent->delete();

        return redirect()->route('assessment_events.index');
    }

    /**
     * Get Assessable Events
     *
     * @return \Illuminate\Http\Response
     */
    public static function getFeedbackEvents(AssessmentEventStudent $assessmentEventStudent): Collection
    {
        $eventIds = AssessmentEventStudent::where('student_id', $assessmentEventStudent->student_id)->get()
            ->pluck('event_id')
            ->unique();

        $assessmentEvents = AssessmentEvent::whereIn('id', $eventIds)
            ->get();

        $notYetSubmittedEvents = $assessmentEvents->filter(function (AssessmentEvent $value, int $key) use ($assessmentEventStudent) {
            $stopTime = $value->getRawOriginal('stop_time') ?? $value->stop_time;

            return ($stopTime >= Carbon::now()->format(config('datetimeformat.date_time_format'))) && (AssessmentStatus::where('event_id', $value->id)->where('student_id', $assessmentEventStudent->student_id)->count() == 0);
        });

        $submittedEvents = $assessmentEvents->filter(function (AssessmentEvent $value, int $key) use ($assessmentEventStudent) {
            return AssessmentStatus::where('event_id', $value->id)->where('student_id', $assessmentEventStudent->student_id)->count();
        });

        return collect(['submitted' => $submittedEvents, 'notYetSubmitted' => $notYetSubmittedEvents]);
    }
}
