<?php

namespace App\Http\Controllers;

use App\Models\assessment_event_student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class StudenLogoutController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, assessment_event_student $assessment_event_student)
    {
        Cache::forget('student_token_' . $assessment_event_student->id);
        $request->session()->forget('student_auth_token_' . $assessment_event_student->id);

        return redirect()->route('student-login-form');
    }
}
