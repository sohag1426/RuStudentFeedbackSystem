<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UsersProfileEditController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create(User $user)
    {
        if ($user->id !== auth()->user()->id) {
            abort(404);
        }

        return view('teacher.profile-edit', [
            'user' => $user,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'mobile' => 'required|string'
        ]);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->mobile = $request->mobile;
        $user->save();

        return redirect()->route('dashboard');
    }
}
