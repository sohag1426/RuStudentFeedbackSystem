<?php

namespace App\Http\Controllers;

use App\Models\log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log as FacadesLog;

class AdminLoginController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.login');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('admin')->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            try {
                $log = new log();
                $log->user_id = auth()->user()->id;
                $log->department_id = auth()->user()->department_id;
                $log->topic = 'Login';
                $log->log = 'Login successful. Time: '.now().' From IP: '.$request->ip().' Browser : '.$request->userAgent();
                $log->model_type = 'App\Models\Admin';
                $log->model_id = auth()->user()->id;
                $log->save();
            } catch (\Throwable $th) {
                FacadesLog::error($th->getMessage());
            }

            return redirect()->route('admin-dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Destroy an authenticated session.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('admin-login');
    }
}
