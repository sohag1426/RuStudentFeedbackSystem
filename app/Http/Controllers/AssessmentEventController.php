<?php

namespace App\Http\Controllers;

use App\Models\assessment_event;
use App\Models\assessment_event_student;
use App\Models\assessment_status;
use App\Models\course;
use App\Models\log;
use App\Models\student_group;
use App\Models\student_group_member;
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
        $assessment_events = assessment_event::with(['teacher', 'course', 'group'])
            ->where('department_id', $request->user()->department_id)
            ->get();
        return view('teacher.assessment_events', [
            'assessment_events' => $assessment_events,
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
                $query->where('role', 'department_admin')
                    ->orWhere('role', 'teacher');
            })->get();

        $courses = course::where('department_id', $request->user()->department_id)->get();
        $groups = student_group::where('department_id', $request->user()->department_id)->get();
        return view('teacher.assessment_events_create', [
            'teachers' => $teachers,
            'courses' => $courses,
            'groups' => $groups,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'teacher_id' => ['required', 'numeric'],
            'course_id' => ['required', 'numeric'],
            'group_id' => 'required|numeric',
            'start_date' => 'required|string',
            'start_hour' => ['required', 'numeric'],
            'start_minute' => ['required', 'numeric'],
            'stop_date' => 'required|string',
            'stop_hour' => ['required', 'numeric'],
            'stop_minute' => ['required', 'numeric'],
        ]);

        $now = Carbon::now('Asia/Dhaka')->setHour(0)->setMinute(0);

        $start_date = date_format(date_create($request->start_date), config('datetimeformat.date_format'));
        $stop_date = date_format(date_create($request->stop_date), config('datetimeformat.date_format'));

        $start_time = Carbon::createFromFormat(config('datetimeformat.date_format'), $start_date);
        $start_time->setHour($request->start_hour)->setMinute($request->start_minute);
        if ($start_time->lessThan($now)) {
            return redirect()->route('assessment_events.create')->with('info', 'Backdated events are not possible to create.');
        }

        $stop_time = Carbon::createFromFormat(config('datetimeformat.date_format'), $stop_date);
        $stop_time->setHour($request->stop_hour)->setMinute($request->stop_minute);
        if ($stop_time->lessThan($start_time)) {
            return redirect()->route('assessment_events.create')->with('info', 'It is not possible to stop before the start time.');
        }

        $assessment_event = new assessment_event();
        $assessment_event->user_id = $request->user()->id;
        $assessment_event->department_id = $request->user()->department_id;
        $assessment_event->teacher_id = $request->teacher_id;
        $assessment_event->course_id = $request->course_id;
        $assessment_event->group_id = $request->group_id;
        $assessment_event->start_time = $start_time;
        $assessment_event->stop_time = $stop_time;
        $assessment_event->save();

        // assessment_event_students
        $student_group_members = student_group_member::where('group_id', $assessment_event->group_id)->get();
        foreach ($student_group_members as $student) {
            $assessment_event_student = new assessment_event_student();
            $assessment_event_student->event_id = $assessment_event->id;
            $assessment_event_student->department_id = $student->department_id;
            $assessment_event_student->group_id = $student->group_id;
            $assessment_event_student->student_id = $student->student_id;
            $assessment_event_student->name = $student->name;
            $assessment_event_student->save();
        }

        return redirect()->route('assessment_events.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\assessment_event  $assessment_event
     * @return \Illuminate\Http\Response
     */
    public function edit(assessment_event $assessment_event)
    {
        $teachers = User::where('department_id', $assessment_event->department_id)
            ->where(function ($query) {
                $query->where('role', 'department_admin')
                    ->orWhere('role', 'teacher');
            })->get();

        $courses = course::where('department_id', $assessment_event->department_id)->get();
        $groups = student_group::where('department_id', $assessment_event->department_id)->get();

        return view('teacher.assessment_events_edit', [
            'assessment_event' => $assessment_event,
            'teachers' => $teachers,
            'courses' => $courses,
            'groups' => $groups,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\assessment_event  $assessment_event
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, assessment_event $assessment_event)
    {
        $request->validate([
            'teacher_id' => ['required', 'numeric'],
            'course_id' => ['required', 'numeric'],
            'group_id' => 'required|numeric',
            'start_date' => 'required|string',
            'start_hour' => ['required', 'numeric'],
            'start_minute' => ['required', 'numeric'],
            'stop_date' => 'required|string',
            'stop_hour' => ['required', 'numeric'],
            'stop_minute' => ['required', 'numeric'],
        ]);

        $now = Carbon::now('Asia/Dhaka')->setHour(0)->setMinute(0);

        $start_date = date_format(date_create($request->start_date), config('datetimeformat.date_format'));
        $stop_date = date_format(date_create($request->stop_date), config('datetimeformat.date_format'));

        $start_time = Carbon::createFromFormat(config('datetimeformat.date_format'), $start_date);
        $start_time->setHour($request->start_hour)->setMinute($request->start_minute);
        if ($start_time->lessThan($now)) {
            return redirect()->route('assessment_events.edit', ['assessment_event' => $assessment_event])->with('info', 'Backdated events are not possible to create.');
        }

        $stop_time = Carbon::createFromFormat(config('datetimeformat.date_format'), $stop_date);
        $stop_time->setHour($request->stop_hour)->setMinute($request->stop_minute);
        if ($stop_time->lessThan($start_time)) {
            return redirect()->route('assessment_events.edit', ['assessment_event' => $assessment_event])->with('info', 'It is not possible to stop before the start time.');
        }

        $assessment_event->teacher_id = $request->teacher_id;
        $assessment_event->course_id = $request->course_id;
        $assessment_event->group_id = $request->group_id;
        $assessment_event->start_time = $start_time;
        $assessment_event->stop_time = $stop_time;
        $assessment_event->save();

        if ($assessment_event->wasChanged('group_id')) {
            assessment_event_student::where('event_id', $assessment_event->id)->delete();
            $student_group_members = student_group_member::where('group_id', $assessment_event->group_id)->get();
            foreach ($student_group_members as $student) {
                $assessment_event_student = new assessment_event_student();
                $assessment_event_student->event_id = $assessment_event->id;
                $assessment_event_student->department_id = $student->department_id;
                $assessment_event_student->group_id = $student->group_id;
                $assessment_event_student->student_id = $student->student_id;
                $assessment_event_student->name = $student->name;
                $assessment_event_student->save();
            }
        }

        // log
        if ($assessment_event->wasChanged('teacher_id') || $assessment_event->wasChanged('course_id') || $assessment_event->wasChanged('group_id')) {
            $log_message = '';
            if ($assessment_event->wasChanged('teacher_id')) {
                $log_message .= 'teacher id was changed from ' . $assessment_event->getOriginal('teacher_id') . ' to ' . $assessment_event->teacher_id;
            }
            if ($assessment_event->wasChanged('course_id')) {
                $log_message .= ' course id was changed from ' . $assessment_event->getOriginal('course_id') . ' to ' . $assessment_event->course_id;
            }

            if ($assessment_event->wasChanged('group_id')) {
                $log_message .= ' studen group id was changed from ' . $assessment_event->getOriginal('group_id') . ' to ' . $assessment_event->group_id;
            }

            $log = new log();
            $log->user_id = $request->user()->id;
            $log->department_id = $request->user()->department_id;
            $log->topic = 'assessment event updated';
            $log->log = $log_message;
            $log->model_type = 'App\Models\assessment_event';
            $log->model_id = $assessment_event->id;
            $log->save();
        }

        return redirect()->route('assessment_events.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\assessment_event  $assessment_event
     * @return \Illuminate\Http\Response
     */
    public function destroy(assessment_event $assessment_event)
    {
        $this->authorize('delete', $assessment_event);
        $assessment_event->delete();

        return redirect()->route('assessment_events.index');
    }

    /**
     * Get Assessable Events
     *
     * @param  \App\Models\assessment_event_student  $assessment_event_student
     * @return \Illuminate\Http\Response
     */
    public static function getFeedbackEvents(assessment_event_student $assessment_event_student): Collection
    {
        $event_ids = assessment_event_student::where('student_id', $assessment_event_student->student_id)->get()
            ->pluck('event_id')
            ->unique();

        $assessment_events = assessment_event::whereIn('id', $event_ids)
            ->get();

        $notYetSubmittedEvents =  $assessment_events->filter(function (assessment_event $value, int $key) use ($assessment_event_student) {
            return ($value->stop_time >=  Carbon::now()->format(config('datetimeformat.date_time_format'))) && (assessment_status::where('event_id', $value->id)->where('student_id', $assessment_event_student->student_id)->count() == 0);
        });

        $submittedEvents =  $assessment_events->filter(function (assessment_event $value, int $key) use ($assessment_event_student) {
            return assessment_status::where('event_id', $value->id)->where('student_id', $assessment_event_student->student_id)->count();
        });

        return collect(['submitted' => $submittedEvents, 'notYetSubmitted' => $notYetSubmittedEvents]);
    }
}
