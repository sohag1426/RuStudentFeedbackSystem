<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UsersProfileEditController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request, User $user)
    {
        if ($user->id !== $request->user()->id) {
            abort(403, 'Unauthorized');
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
        if ($user->id !== $request->user()->id) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'mobile' => ['required', 'string', 'max:30'],
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->mobile = $request->mobile;
        $user->save();

        return redirect()->route('dashboard');
    }
}
