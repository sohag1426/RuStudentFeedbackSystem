<?php

namespace App\Http\Controllers;

use App\Models\assessment_event;
use App\Models\department;
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

            $assessment_events = assessment_event::with(['teacher', 'course', 'group'])
                ->where($filter)
                ->orderBy('id', 'desc')
                ->paginate(50)
                ->withQueryString();

            $selectedDepartment = department::find($request->department_id);
        } else {
            $assessment_events = assessment_event::with(['teacher', 'course', 'group'])
                ->orderBy('id', 'desc')
                ->paginate(20);

            $selectedDepartment = null;
        }

        $departments = department::all();

        return view('admin.dashboard', [
            'assessment_events' => $assessment_events,
            'departments' => $departments,
            'selectedDepartment' => $selectedDepartment,
        ]);
    }
}
