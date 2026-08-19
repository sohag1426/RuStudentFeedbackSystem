<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\department;
use App\Models\User;
use App\Models\assessment_event;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminReportController extends Controller
{
    public function departmentIndex()
    {
        $departments = department::all();
        return view('admin.reports.department', compact('departments'));
    }

    public function teacherIndex(Request $request)
    {
        $departments = department::all();
        $query = User::where('role', 'teacher');
        
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }
        
        $teachers = $query->get();
        return view('admin.reports.teacher', compact('teachers', 'departments'));
    }

    public function departmentDownload(department $department)
    {
        $events = assessment_event::where('department_id', $department->id)
            ->with(['teacher', 'course'])
            ->get();
            
        if ($events->isEmpty()) {
            return back()->with('error', 'No assessment events found for this department.');
        }

        foreach ($events as $event) {
            if ($event->score === 'undefined') {
                \App\Services\ScoreService::generateScore($event);
            }
        }
            
        $pdf = Pdf::loadView('admin.reports.pdf_department', compact('department', 'events'));
        return $pdf->download('report-department-' . $department->en_name . '.pdf');
    }

    public function teacherDownload(User $teacher)
    {
        $events = assessment_event::where('teacher_id', $teacher->id)
            ->with(['department', 'course'])
            ->get();
            
        if ($events->isEmpty()) {
            return back()->with('error', 'No assessment events found for this teacher.');
        }

        foreach ($events as $event) {
            if ($event->score === 'undefined') {
                \App\Services\ScoreService::generateScore($event);
            }
        }
            
        $pdf = Pdf::loadView('admin.reports.pdf_teacher', compact('teacher', 'events'));
        return $pdf->download('report-teacher-' . $teacher->name . '.pdf');
    }
}
