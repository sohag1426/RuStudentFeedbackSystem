<?php

namespace App\Jobs;

use App\Models\AssessmentEvent;
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
     * @var \App\Models\AssessmentEvent
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
    public function __construct(AssessmentEvent $assessment_event)
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
        \App\Services\ScoreService::generateScore($this->assessment_event);
    }
}
