<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\AssessmentEvent;
use App\Models\AssessmentStatus;
use App\Models\DetailedScore;
use Illuminate\Support\Facades\DB;

class ScoreService
{
    /**
     * Generate score for a given assessment event and update group metrics for all related events.
     */
    public static function generateScore(AssessmentEvent $assessment_event)
    {
        $assessments = Assessment::where('department_id', $assessment_event->department_id)
            ->where('event_id', $assessment_event->id)
            ->get();

        $assessments_count = $assessments->count();
        if ($assessments_count < 1) {
            return;
        }

        DB::transaction(function () use ($assessment_event, $assessments, $assessments_count) {
            $total_score = $assessments->sum('score');
            $average_score = $total_score / $assessments_count;

            $distinct_students = AssessmentStatus::where('event_id', $assessment_event->id)->count();

            $assessment_event->score = round($average_score, 2);
            $assessment_event->assessment_count = $distinct_students > 0 ? $distinct_students : max(1, $assessments->groupBy('student_id')->count());
            $assessment_event->save();

            // detailed score
            DetailedScore::where('event_id', $assessment_event->id)->delete();
            $assessments_by_group = $assessments->groupBy('question_id');
            foreach ($assessments_by_group as $question_id => $assessments_group) {
                $assessments_group_count = $assessments_group->count();
                $assessments_group_total_score = $assessments_group->sum('score');
                $group_average = $assessments_group_total_score / $assessments_group_count;

                $detailed_score = new DetailedScore();
                $detailed_score->department_id = $assessment_event->department_id;
                $detailed_score->event_id = $assessment_event->id;
                $detailed_score->question_id = $question_id;
                $detailed_score->assessment_count = $assessments_group_count;
                $detailed_score->score = round($group_average, 2);
                $detailed_score->save();
            }

            // Update group_average, min, max for ALL events in the same group that have been generated
            $group_events = AssessmentEvent::where('group_id', $assessment_event->group_id)
                ->where('score', '!=', 'undefined')
                ->get();

            if ($group_events->count() > 0) {
                $group_sum = $group_events->sum('score');
                $group_average = round($group_sum / $group_events->count(), 2);
                $group_highest = round($group_events->max('score'), 2);
                $group_lowest = round($group_events->min('score'), 2);

                foreach ($group_events as $event) {
                    $event->group_average = $group_average;
                    $event->group_highest = $group_highest;
                    $event->group_lowest = $group_lowest;
                    $event->save();

                    if ($event->id === $assessment_event->id) {
                        $assessment_event->group_average = $group_average;
                        $assessment_event->group_highest = $group_highest;
                        $assessment_event->group_lowest = $group_lowest;
                    }
                }
            }
        });
    }
}
