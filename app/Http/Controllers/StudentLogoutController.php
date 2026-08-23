<?php

namespace App\Http\Controllers;

use App\Models\AssessmentEventStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class StudentLogoutController extends Controller
{
    /**
     * Store a newly created resource in storage (logout action).
     */
    public function store(Request $request, AssessmentEventStudent $assessment_event_student)
    {
        Cache::forget('student_token_' . $assessment_event_student->id);
        $request->session()->forget('student_auth_token_' . $assessment_event_student->id);

        return redirect()->route('student-login-form');
    }
}
