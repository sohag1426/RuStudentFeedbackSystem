<?php

namespace App\Http\Controllers;

use App\Models\log;
use App\Models\student_group;
use Illuminate\Http\Request;

class StudentGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $student_groups = student_group::with('members')->where('department_id', $request->user()->department_id)->get();

        return view('teacher.student_groups', [
            'student_groups' => $student_groups,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('teacher.student_group_create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
        ]);

        $student_group = new student_group();
        $student_group->user_id = $request->user()->id;
        $student_group->department_id = $request->user()->department_id;
        $student_group->name = $request->name;
        $student_group->save();

        return redirect()->route('student_groups.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(student_group $student_group)
    {
        return view('teacher.student_group_edit', [
            'student_group' => $student_group,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, student_group $student_group)
    {
        $request->validate([
            'name' => 'required|string',
        ]);

        $student_group->name = $request->name;
        $student_group->save();

        // log
        if ($student_group->wasChanged('name')) {
            if ($student_group->wasChanged('name')) {
                $log_message = 'student group name was changed from '.$student_group->getOriginal('name').' to '.$student_group->name;
            }
            $log = new log();
            $log->user_id = $request->user()->id;
            $log->department_id = $request->user()->department_id;
            $log->topic = 'student group updated';
            $log->log = $log_message;
            $log->model_type = 'App\Models\student_group';
            $log->model_id = $student_group->id;
            $log->save();
        }

        return redirect()->route('student_groups.index');
    }
}
