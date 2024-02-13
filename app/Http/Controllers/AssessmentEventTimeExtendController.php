<?php

namespace App\Http\Controllers;

use App\Models\assessment_event;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AssessmentEventTimeExtendController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create(assessment_event $assessment_event)
    {
        $this->authorize('update', [$assessment_event]);
        return view('teacher.assessment_event_extend', [
            'assessment_event' => $assessment_event,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, assessment_event $assessment_event)
    {
        $this->authorize('update', [$assessment_event]);

        $request->validate([
            'stop_date' => 'required|string',
            'stop_hour' => ['required', 'numeric'],
            'stop_minute' => ['required', 'numeric'],
        ]);

        $stop_date = date_format(date_create($request->stop_date), config('datetimeformat.date_format'));
        $stop_time = Carbon::createFromFormat(config('datetimeformat.date_format'), $stop_date);
        $stop_time->setHour($request->stop_hour)->setMinute($request->stop_minute);

        $assessment_event->stop_time = $stop_time;
        $assessment_event->save();

        return redirect()->route('assessment_events.index');
    }
}
