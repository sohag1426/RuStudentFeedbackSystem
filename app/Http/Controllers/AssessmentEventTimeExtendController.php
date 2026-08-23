<?php

namespace App\Http\Controllers;

use App\Models\AssessmentEvent;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AssessmentEventTimeExtendController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create(AssessmentEvent $assessment_event)
    {
        $this->authorize('update', [$assessment_event]);
        return view('teacher.assessment_event_extend', [
            'assessment_event' => $assessment_event,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, AssessmentEvent $assessment_event)
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

        $start_time = Carbon::parse($assessment_event->start_time);
        $now = Carbon::now('Asia/Dhaka');

        if ($stop_time->lessThanOrEqualTo($start_time)) {
            return redirect()->route('assessment_events.extend_time.create', ['assessment_event' => $assessment_event])
                ->with('info', 'Stop time must be after the start time.');
        }

        if ($stop_time->lessThan($now)) {
            return redirect()->route('assessment_events.extend_time.create', ['assessment_event' => $assessment_event])
                ->with('info', 'Stop time must be in the future.');
        }

        $assessment_event->stop_time = $stop_time;
        $assessment_event->save();

        return redirect()->route('assessment_events.index');
    }
}
