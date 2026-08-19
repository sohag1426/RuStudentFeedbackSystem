<?php

namespace App\Http\Controllers;

use App\Models\assessment;
use App\Models\assessment_event;
use App\Models\detailed_score;
use Illuminate\Http\Request;

class ScoreGenerateController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, assessment_event $assessment_event)
    {
        $this->authorize('generateReport', [$assessment_event]);

        \App\Services\ScoreService::generateScore($assessment_event);

        return redirect()->route('assessment_events.index')->with('info', 'Report Generated Successfully');
    }
}
