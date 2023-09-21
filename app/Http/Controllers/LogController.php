<?php

namespace App\Http\Controllers;

use App\Models\log;
use Illuminate\Http\Request;

class LogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $logs = log::where('department_id', $request->user()->department_id)->get();

        return view('teacher.logs', [
            'logs' => $logs,
        ]);
    }
}
