<?php

namespace App\Http\Controllers;

use App\Models\Log;
use Illuminate\Http\Request;

class LogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $logs = Log::where('department_id', $request->user()->department_id)->get();

        return view('teacher.logs', [
            'logs' => $logs,
        ]);
    }
}
