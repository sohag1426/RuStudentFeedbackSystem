<?php

namespace App\Http\Controllers;

use App\Models\AssessmentEvent;
use App\Models\Department;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class AdminReportController extends Controller
{
    public function departmentIndex()
    {
        $departments = Department::all();
        return view('admin.reports.department', compact('departments'));
    }

    public function teacherIndex(Request $request)
    {
        $departments = Department::all();
        $query = User::where('role', 'teacher')->withCount('assessment_events');
        
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }
        
        $teachers = $query->paginate(50)->withQueryString();
        return view('admin.reports.teacher', compact('teachers', 'departments'));
    }

    public function departmentDownload(Department $department)
    {
        $events = AssessmentEvent::where('department_id', $department->id)
            ->with(['teacher', 'course', 'group'])
            ->orderBy('teacher_id')
            ->get();
            
        if ($events->isEmpty()) {
            return back()->with('error', 'No assessment events found for this department.');
        }

        foreach ($events as $event) {
            if ($event->score === 'undefined' || $event->feedback_percentage == 0) {
                \App\Services\ScoreService::generateScore($event);
            }
        }
            
        $pdf = Pdf::loadView('admin.reports.pdf_department', compact('department', 'events'));
        return $pdf->download('report-department-' . $department->en_name . '.pdf');
    }

    public function teacherDownload(User $teacher)
    {
        $events = AssessmentEvent::where('teacher_id', $teacher->id)
            ->with(['department', 'course', 'group'])
            ->get();
            
        if ($events->isEmpty()) {
            return back()->with('error', 'No assessment events found for this teacher.');
        }

        foreach ($events as $event) {
            if ($event->score === 'undefined' || $event->feedback_percentage == 0) {
                \App\Services\ScoreService::generateScore($event);
            }
        }
            
        $pdf = Pdf::loadView('admin.reports.pdf_teacher', compact('teacher', 'events'));
        return $pdf->download('report-teacher-' . $teacher->name . '.pdf');
    }
}
