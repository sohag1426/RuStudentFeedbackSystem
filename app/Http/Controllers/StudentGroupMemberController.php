<?php

namespace App\Http\Controllers;

use App\Models\student_group;
use App\Models\student_group_member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\SimpleExcel\SimpleExcelReader;

class StudentGroupMemberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(student_group $student_group)
    {
        return view('teacher.group-members', ['student_group_members' => $student_group->members]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(student_group $student_group)
    {
        return view('teacher.group-members-create', ['student_group' => $student_group]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, student_group $student_group)
    {
        $request->validate([
            'excel_file' => 'mimes:xlsx',
        ]);

        $file = $request->file('excel_file')->store('group_members');

        $path = Storage::path($file);

        $rows = SimpleExcelReader::create($path)->getRows()->toArray();

        foreach ($rows as $row) {
            $keys_found = array_keys($row);
            $expected_keys = [
                'student_id',
                'name',
            ];
            $key_diff = array_diff($expected_keys, $keys_found);
            if (count($key_diff)) {
                continue;
            }

            if (student_group_member::where('group_id', $student_group->id)->where('student_id', $row['student_id'])->exists()) {
                continue;
            }

            student_group_member::updateOrCreate(
                ['department_id' => $request->user()->department_id, 'group_id' => $student_group->id, 'student_id' => trim($row['student_id'])],
                ['name' => trim($row['name'])]
            );
        }

        Storage::delete($path);

        return redirect()->route('student_groups.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\student_group  $student_group
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, student_group_member $student_group_member)
    {
        if ($request->user()->department_id == $student_group_member->department_id) {
            $student_group_member->delete();
        }

        return redirect()->route('student_groups.index');
    }
}
