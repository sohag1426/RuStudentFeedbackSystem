<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ChangePasswordController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        return match ($request->user()->role) {
            'admin', 'SuperAdmin' => view('admin.change-password'),
            'teacher', 'DepartmentChair', 'DepartmentManager' => view('teacher.change-password'),
            default => view('teacher.change-password'),
        };
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $rules = [
            'password' => ['required', 'confirmed', Password::min(8)],
        ];

        if (! empty($user->password)) {
            $rules['current_password'] = ['required', 'current_password'];
        }

        $request->validate($rules);

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('dashboard')->with('success', 'Password updated successfully!');
    }
}
