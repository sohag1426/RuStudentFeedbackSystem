<?php

namespace App\Http\Controllers;

use App\Enums\Semester;
use App\Enums\Year;
use App\Models\Course;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $courses = Course::where('department_id', $request->user()->department_id)->get();

        return view('teacher.courses', [
            'courses' => $courses,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('teacher.courses-create', [
            'years' => Year::cases(),
            'semesters' => Semester::cases(),
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
            'code' => 'required|string',
            'name' => 'required|string',
            'year' => ['required', new Enum(Year::class)],
            'semester' => ['required', new Enum(Semester::class)],
        ]);

        if (Course::where('department_id', $request->user()->department_id)->where('code', $request->code)->count()) {
            return redirect()->route('courses.index')->with('info', 'Duplicate Course Code');
        }

        if (Course::where('department_id', $request->user()->department_id)->where('name', $request->name)->count()) {
            return redirect()->route('courses.index')->with('info', 'Duplicate Course Name');
        }

        $course = new Course;
        $course->user_id = $request->user()->id;
        $course->department_id = $request->user()->department_id;
        $course->code = $request->code;
        $course->name = $request->name;
        $course->year = $request->year;
        $course->semester = $request->semester;
        $course->save();

        return redirect()->route('courses.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Course $course)
    {
        return view('teacher.courses-edit', [
            'course' => $course,
            'years' => Year::cases(),
            'semesters' => Semester::cases(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Course $course)
    {
        $request->validate([
            'code' => 'required|string',
            'name' => 'required|string',
            'year' => ['required', new Enum(Year::class)],
            'semester' => ['required', new Enum(Semester::class)],
        ]);

        if ($request->code != $course->code) {
            if (Course::where('department_id', $request->user()->department_id)->where('code', $request->code)->count()) {
                return redirect()->route('courses.index')->with('info', 'Duplicate Course Code');
            }
        }

        if ($request->name != $course->name) {
            if (Course::where('department_id', $request->user()->department_id)->where('name', $request->name)->count()) {
                return redirect()->route('courses.index')->with('info', 'Duplicate Course Name');
            }
        }

        $course->code = $request->code;
        $course->name = $request->name;
        $course->year = $request->year;
        $course->semester = $request->semester;
        $course->save();

        return redirect()->route('courses.index');
    }

    /**
     * Display change logs for courses in the user's department.
     *
     * @return \Illuminate\Http\Response
     */
    public function changeLogs(Request $request)
    {
        $query = Log::where('department_id', $request->user()->department_id)
            ->where(function ($q) {
                $q->where('model_type', Course::class)
                    ->orWhere('model_type', 'App\Models\Course')
                    ->orWhere('topic', 'like', 'course %');
            });

        if ($request->filled('course_id')) {
            $query->where('model_id', $request->course_id);
        }

        $logs = $query->latest()->get();

        return view('teacher.courses-logs', [
            'logs' => $logs,
        ]);
    }

    /**
     * Display change logs for a specific course.
     *
     * @return \Illuminate\Http\Response
     */
    public function courseChangeLogs(Request $request, Course $course)
    {
        $this->authorize('view', $course);

        $logs = Log::where('department_id', $request->user()->department_id)
            ->where(function ($query) use ($course) {
                $query->where(function ($q) use ($course) {
                    $q->where('model_type', Course::class)
                        ->where('model_id', $course->id);
                })->orWhere(function ($q) use ($course) {
                    $q->where('model_id', $course->id)
                        ->where('topic', 'like', 'course %');
                });
            })
            ->latest()
            ->get();

        return view('teacher.course-logs', [
            'course' => $course,
            'logs' => $logs,
        ]);
    }
}
