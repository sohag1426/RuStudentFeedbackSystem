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
            $assessment_events = assessment_event::with(['teacher', 'course', 'group'])->where('department_id', $request->department_id)->orderBy('id', 'desc')->paginate(20);
            $selectedDepartment =  department::find($request->department_id);
        } else {
            $assessment_events = assessment_event::with(['teacher', 'course', 'group'])->orderBy('id', 'desc')->paginate(20);
            $selectedDepartment =  department::make([
                'id' => '',
                'en_name' => 'select department'
            ]);
        }

        $departments = department::all();
        return view('admin.dashboard', [
            'assessment_events' => $assessment_events,
            'departments' => $departments,
            'selectedDepartment' => $selectedDepartment
        ]);
    }
}
