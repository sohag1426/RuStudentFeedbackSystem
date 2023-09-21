<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
            'admin' => view('admin.change-password'),
            'teacher' => view('teacher.change-password'),
            default => 'Not Found',
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

        $request->validate([
            'password' => 'required|confirmed|min:8',
        ]);

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('dashboard')->with('success', 'Password updated successfully!');
    }
}
