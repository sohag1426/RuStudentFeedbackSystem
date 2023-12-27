<?php

namespace App\Http\Controllers;

use App\Models\course;
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

        if (course::where('department_id', $request->user()->department_id)->where('code', $request->code)->count()) {
            return redirect()->route('courses.index')->with('info', 'Duplicate Course Code');
        }

        if (course::where('department_id', $request->user()->department_id)->where('name', $request->name)->count()) {
            return redirect()->route('courses.index')->with('info', 'Duplicate Course Name');
        }

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

        if ($request->code != $course->code) {
            if (course::where('department_id', $request->user()->department_id)->where('code', '==', $request->code)->count()) {
                return redirect()->route('courses.index')->with('info', 'Duplicate Course Code');
            }
        }

        if ($request->name != $course->name) {
            if (course::where('department_id', $request->user()->department_id)->where('name', '==', $request->name)->count()) {
                return redirect()->route('courses.index')->with('info', 'Duplicate Course Name');
            }
        }

        $course->code = $request->code;
        $course->name = $request->name;
        $course->save();

        return redirect()->route('courses.index');
    }
}
