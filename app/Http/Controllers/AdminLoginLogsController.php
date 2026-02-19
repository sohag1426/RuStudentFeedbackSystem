<?php

namespace App\Http\Controllers;

use App\Models\log;
use Illuminate\Http\Request;

class AdminLoginLogsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $admin = auth('admin')->user();
        $logs = log::where('model_id', $admin->id)
            ->where('model_type', 'App\Models\Admin')
            ->where('topic', 'Login')
            ->get();

        return view('admin.login-logs', [
            'logs' => $logs,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
