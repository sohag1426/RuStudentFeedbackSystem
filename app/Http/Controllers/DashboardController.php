<?php

namespace App\Http\Controllers;

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

        return match ($request->user()->role) {
            'teacher', 'DepartmentChair', 'DepartmentManager' => view('teacher.dashboard'),
            default => 'Not Found',
        };
    }
}
