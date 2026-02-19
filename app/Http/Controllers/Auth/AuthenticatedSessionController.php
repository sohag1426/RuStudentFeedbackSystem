<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\StudentLoginController;
use App\Models\log;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log as FacadesLog;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.login-v2');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'internet_id' => 'required|numeric',
            'password' => 'required|string',
        ]);

        if (User::where('internet_id', $request->internet_id)->count() == 0) {
            return back()->withErrors([
                'internet_id' => 'The provided Internet Id does not match our records.',
            ]);
        }

        $user = User::where('internet_id', $request->internet_id)->first();

        $verify = StudentLoginController::verify($user->internet_id, $request->password);
        if ($verify['error_code'] !== 0) {
            return back()->withErrors([
                'password' => 'The provided password does not match our records.',
            ]);
        }

        Auth::login($user, $remember = true);

        $request->session()->regenerate();

        try {
            $log = new log();
            $log->user_id = auth()->user()->id;
            $log->department_id = auth()->user()->department_id;
            $log->topic = 'Login';
            $log->log = 'Login successful. Time: '.now().' From IP: '.$request->ip().' Browser : '.$request->userAgent();
            $log->model_type = 'App\Models\User';
            $log->model_id = $user->id;
            $log->save();
        } catch (\Throwable $th) {
            FacadesLog::error($th->getMessage());
        }

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
