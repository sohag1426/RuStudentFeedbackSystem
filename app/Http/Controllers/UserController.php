<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $users = User::where('department_id', $request->user()->department_id)
            ->where(function ($query) {
                $query->where('role', 'department_admin')
                    ->orWhere('role', 'teacher');
            })->get();

        return view('teacher.teachers', [
            'users' => $users,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('teacher.teachers-create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => 'required',
        ]);

        $user = new User();
        $user->user_id = $request->user()->id;
        $user->department_id = $request->user()->department_id;
        $user->role = config('app.user_default_role');
        $user->name = $request->name;
        $user->email = $request->email;
        $user->email_verified_at = Carbon::now();
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('users.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return view('teacher.teachers-edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        if ($request->email !== $user->email) {
            if (User::where('email', $request->email)->count()) {
                return redirect()->route('users.index')->with('error', 'Duplicate Email Address!');
            }
        }

        $user->email = $request->email;
        $user->name = $request->name;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        return redirect()->route('users.index')->with('success', 'Teacher has been edited successfully!');
    }
}
