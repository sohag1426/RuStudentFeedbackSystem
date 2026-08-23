<?php

namespace App\Http\Controllers;

use App\Models\AssessmentEvent;
use App\Models\Department;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->filled('department_id')) {
            // filter
            $filter = [];
            $filter[] = ['department_id', '=', $request->department_id];
            if ($request->filled('group_id')) {
                $filter[] = ['group_id', '=', $request->group_id];
            }

            $assessment_events = AssessmentEvent::with(['teacher', 'course', 'group'])
                ->where($filter)
                ->orderBy('id', 'desc')
                ->paginate(50)
                ->withQueryString();

            $selectedDepartment = Department::find($request->department_id);
        } else {
            $assessment_events = AssessmentEvent::with(['teacher', 'course', 'group'])
                ->orderBy('id', 'desc')
                ->paginate(20);

            $selectedDepartment = null;
        }

        $departments = Department::all();

        return view('admin.dashboard', [
            'assessment_events' => $assessment_events,
            'departments' => $departments,
            'selectedDepartment' => $selectedDepartment,
        ]);
    }
}
