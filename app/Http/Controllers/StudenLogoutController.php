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
        $key = $assessment_event_student->id;
        if (Cache::has($key)) {
            Cache::forget($key);
        }

        return redirect()->route('student-login-form');
    }
}
