<?php

namespace App\Http\Controllers;

use App\Models\AssessmentEvent;
use Illuminate\Http\Request;

class ScoreGenerateController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AssessmentEvent  $assessment_event
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, AssessmentEvent $assessment_event)
    {
        $this->authorize('generateReport', [$assessment_event]);

        \App\Services\ScoreService::generateScore($assessment_event);

        return redirect()->route('assessment_events.index')->with('info', 'Report Generated Successfully');
    }
}
