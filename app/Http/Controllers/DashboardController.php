<?php

namespace App\Http\Controllers;

use App\Models\log;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (is_null($request->user()->email)) {
            return redirect()->route('users.profile.create', ['user' => $request->user()]);
        }

        $logs = log::where('department_id', $request->user()->department_id)
            ->where('topic', 'Login')
            ->where('model_id', $request->user()->id)
            ->get();

        return match ($request->user()->role) {
            'teacher', 'DepartmentChair', 'DepartmentManager' => view('teacher.dashboard', [
                'logs' => $logs,
            ]),
            default => 'Not Found',
        };
    }
}
