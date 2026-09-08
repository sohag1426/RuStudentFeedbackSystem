<?php

namespace App\Console\Commands;

use App\Models\Assessment;
use App\Models\AssessmentEvent;
use App\Services\ScoreService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerateReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:generate
                            {--all : Generate reports for all assessment events}
                            {--event= : Specific assessment event ID to generate report for}
                            {--date= : Reference date to determine running or ended one day before}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate assessment reports/scores inline for running events and events that ended one day before';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        @set_time_limit(0);

        $all = (bool) $this->option('all');
        $eventId = $this->option('event');
        $date = $this->option('date') ? Carbon::parse($this->option('date')) : Carbon::now();

        $this->info("Starting inline report generation at {$date->toDateTimeString()}...");

        $query = AssessmentEvent::query();

        if ($eventId) {
            $query->where('id', $eventId);
        } elseif (! $all) {
            $query->runningOrEndedOneDayBefore($date);
        }

        $events = $query->get();

        if ($events->isEmpty()) {
            $this->info('No assessment events found matching the criteria.');

            return Command::SUCCESS;
        }

        $this->info("Found {$events->count()} assessment event(s) to process inline.");

        $processed = 0;
        $skipped = 0;

        foreach ($events as $event) {
            if (! Assessment::where('event_id', $event->id)->exists()) {
                $this->line("Event #{$event->id}: No submitted assessments found. Skipped.");
                $skipped++;

                continue;
            }

            try {
                // Generate report synchronously/inline without queue worker
                ScoreService::generateScore($event);
                $this->line("Event #{$event->id}: Report generated inline successfully.");
                $processed++;
            } catch (\Throwable $th) {
                $this->error("Event #{$event->id}: Failed to generate report. Error: {$th->getMessage()}");
                Log::error("Failed to generate report for assessment event #{$event->id}: ".$th->getMessage(), [
                    'exception' => $th,
                ]);
            }
        }

        $this->info("Inline report generation completed. Processed: {$processed}, Skipped: {$skipped}.");
        Log::info("Scheduled inline report generation completed. Processed: {$processed}, Skipped: {$skipped}.");

        return Command::SUCCESS;
    }
}
