<?php

namespace App\Jobs;

use App\Models\assessment;
use App\Models\assessment_event;
use App\Models\detailed_score;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ScoreGenerateJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The assessment_event instance.
     *
     * @var \App\Models\assessment_event
     */
    public $assessment_event;

    /**
     * The number of seconds after which the job's unique lock will be released.
     *
     * @var int
     */
    public $uniqueFor = 3600;

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId()
    {
        return $this->assessment_event->id;
    }

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(assessment_event $assessment_event)
    {
        $this->assessment_event = $assessment_event;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $assessment_event = $this->assessment_event;

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

        $assessment_event->score = $average_score;
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
            $detailed_score->score = $average_score;
            $detailed_score->save();
        }

        // group_average | min | max
        $assessment_events = assessment_event::where('group_id', $assessment_event->group_id)
            ->where('score', '!=', 'undefined')
            ->get();
        $group_sum = $assessment_events->sum('score');
        $group_average = $group_sum / $assessment_events->count();
        $assessment_event->group_average = $group_average;
        $assessment_event->group_highest = $assessment_events->max('score');
        $assessment_event->group_lowest = $assessment_events->min('score');
        $assessment_event->save();
    }
}
