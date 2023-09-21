<?php

namespace App\Http\Controllers;

use App\Models\course;
use App\Models\log;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $courses = course::where('department_id', $request->user()->department_id)->get();

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
        return view('teacher.courses-create');
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
            'code' => 'required|string',
            'name' => 'required|string',
        ]);

        $course = new course();
        $course->user_id = $request->user()->id;
        $course->department_id = $request->user()->department_id;
        $course->code = $request->code;
        $course->name = $request->name;
        $course->save();

        return redirect()->route('courses.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\course  $course
     * @return \Illuminate\Http\Response
     */
    public function edit(course $course)
    {
        return view('teacher.courses-edit', [
            'course' => $course,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\course  $course
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, course $course)
    {
        $request->validate([
            'code' => 'required|string',
            'name' => 'required|string',
        ]);

        $course->code = $request->code;
        $course->name = $request->name;
        $course->save();

        // log
        if ($course->wasChanged('code') || $course->wasChanged('name')) {
            $log_message = '';
            if ($course->wasChanged('code')) {
                $log_message .= 'course code was changed from ' . $course->getOriginal('code') . ' to ' . $course->code;
            }
            if ($course->wasChanged('name')) {
                $log_message .= ' course name was changed from ' . $course->getOriginal('name') . ' to ' . $course->name;
            }

            $log = new log();
            $log->user_id = $request->user()->id;
            $log->department_id = $request->user()->department_id;
            $log->topic = 'course updated';
            $log->log = $log_message;
            $log->model_type = 'App\Models\course';
            $log->model_id = $course->id;
            $log->save();
        }
        return redirect()->route('courses.index');
    }
}
