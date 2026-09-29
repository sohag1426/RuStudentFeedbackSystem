<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\StudentGroup;
use App\Services\SessionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Spatie\SimpleExcel\SimpleExcelWriter;

class StudentGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $student_groups = StudentGroup::with('members')->where('department_id', $request->user()->department_id)->get();

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
        return view('teacher.student_group_create', [
            'sessions' => SessionService::getSessions(),
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
            'name' => 'required|string',
            'session' => 'required|string',
        ]);

        if (StudentGroup::where('department_id', $request->user()->department_id)
            ->where('name', $request->name)
            ->where('session', $request->session)
            ->exists()) {
            return redirect()->route('student_groups.index')->with('info', 'Duplicate Student Group');
        }

        $student_group = new StudentGroup;
        $student_group->user_id = $request->user()->id;
        $student_group->department_id = $request->user()->department_id;
        $student_group->name = $request->name;
        $student_group->session = $request->session;
        $student_group->save();

        return redirect()->route('student_groups.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(StudentGroup $student_group)
    {
        return view('teacher.student_group_edit', [
            'student_group' => $student_group,
            'sessions' => SessionService::getDropdownSessions($student_group->session),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, StudentGroup $student_group)
    {
        $request->validate([
            'name' => 'required|string',
            'session' => 'required|string',
        ]);

        if (StudentGroup::where('department_id', $request->user()->department_id)
            ->where('name', $request->name)
            ->where('session', $request->session)
            ->where('id', '!=', $student_group->id)
            ->exists()) {
            return redirect()->route('student_groups.index')->with('info', 'Duplicate Student Group');
        }

        $oldName = $student_group->name;

        $student_group->name = $request->name;
        $student_group->session = $request->session;
        $student_group->save();

        // log
        if ($student_group->wasChanged(['name', 'session'])) {
            $log_message = 'student group was changed from '.$oldName.' to '.$student_group->name;
            $log = new Log;
            $log->user_id = $request->user()->id;
            $log->department_id = $request->user()->department_id;
            $log->topic = 'student group updated';
            $log->log = $log_message;
            $log->model_type = StudentGroup::class;
            $log->model_id = $student_group->id;
            $log->save();
        }

        return redirect()->route('student_groups.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, StudentGroup $student_group)
    {
        Gate::authorize('delete', $student_group);

        // Delete the student group
        $student_group->delete();

        // Log the deletion
        $log = new Log;
        $log->user_id = $request->user()->id;
        $log->department_id = $request->user()->department_id;
        $log->topic = 'student group deleted';
        $log->log = 'student group deleted: '.$student_group->display_name;
        $log->model_type = StudentGroup::class;
        $log->model_id = $student_group->id;
        $log->save();

        return redirect()->route('student_groups.index')->with('success', 'Student group deleted successfully');
    }

    /**
     * Export the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function export(StudentGroup $student_group)
    {
        $groupTitle = $student_group->session ? ($student_group->name.'-'.$student_group->session) : $student_group->name;
        $fileName = 'students-'.$groupTitle.'.xlsx';
        $writer = SimpleExcelWriter::streamDownload($fileName);

        foreach ($student_group->members as $member) {
            $writer->addRow([
                'student_id' => $member->student_id,
                'name' => $member->name,
            ]);
        }

        return $writer->toBrowser();
    }
}
