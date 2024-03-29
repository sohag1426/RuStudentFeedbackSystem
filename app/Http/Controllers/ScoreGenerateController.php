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

        $assessments = assessment::where('department_id', $assessment_event->department_id)
            ->where('event_id', $assessment_event->id)
            ->get();

        // summary
        $assessments_count = $assessments->count();
        if ($assessments_count < 1) {
            return 0;
        }
        $total_score = $assessments->sum('score');
        $average_score = $total_score / $assessments_count;

        $assessment_event->score = round($average_score, 2);
        $assessment_event->assessment_count = $assessments_count;
        $assessment_event->save();

        // detailed score
        detailed_score::where('event_id', $assessment_event->id)->delete();
        $assessments_by_group = $assessments->groupBy('question_id');
        foreach ($assessments_by_group as $question_id => $assessments_group) {

            $assessments_group_count = $assessments_group->count();
            $assessments_group_total_score = $assessments_group->sum('score');
            $average_score = $assessments_group_total_score / $assessments_group_count;

            $detailed_score = new detailed_score();
            $detailed_score->department_id = $assessment_event->department_id;
            $detailed_score->event_id = $assessment_event->id;
            $detailed_score->question_id = $question_id;
            $detailed_score->assessment_count = $assessments_group_count;
            $detailed_score->score = round($average_score, 2);
            $detailed_score->save();
        }

        // group_average | min | max
        $assessment_events = assessment_event::where('group_id', $assessment_event->group_id)
            ->where('score', '!=', 'undefined')
            ->get();
        $group_sum = $assessment_events->sum('score');
        $group_average = $group_sum / $assessment_events->count();
        $assessment_event->group_average =  round($group_average, 2);
        $assessment_event->group_highest = round($assessment_events->max('score'), 2);
        $assessment_event->group_lowest = round($assessment_events->min('score'), 2);
        $assessment_event->save();

        return redirect()->route('assessment_events.index')->with('info', 'Report Generated Successfully');
    }
}
