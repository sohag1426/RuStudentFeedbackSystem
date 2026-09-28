<?php

namespace App\Console\Commands;

use App\Models\AssessmentEvent;
use App\Models\StudentGroup;
use Illuminate\Console\Command;

class TempFillAssessmentEventsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:temp-fill-assessment-events
                            {--session=2024-2025 : The session of the student groups (default: 2024-2025)}
                            {--year=1st Year : The assessment event year to fill (default: 1st Year)}
                            {--semester=1st Semester : The assessment event semester to fill (default: 1st Semester)}
                            {--force : Overwrite existing non-null values as well}
                            {--dry-run : Simulate the fill without saving changes}';

    /**
     * The console command aliases.
     *
     * @var array<string>
     */
    protected $aliases = [
        'temp:fill-assessment-events',
        'app:fill-assessment-events-2024-2025',
    ];

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Temp command to fill null session, year, and semester values on assessment_events for student groups of session 2024-2025';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $session = (string) $this->option('session');
        $year = (string) $this->option('year');
        $semester = (string) $this->option('semester');
        $force = (bool) $this->option('force');
        $dryRun = (bool) $this->option('dry-run');

        $this->info("Looking up StudentGroups with session '{$session}'...");

        $groups = StudentGroup::where('session', $session)->get();

        if ($groups->isEmpty()) {
            // Also check if any group name contains the session
            $groups = StudentGroup::where('name', 'like', "%{$session}%")->get();
            if ($groups->isNotEmpty()) {
                $this->info("Found {$groups->count()} StudentGroup(s) matching '{$session}' by name.");
            }
        }

        if ($groups->isEmpty()) {
            $this->warn("No StudentGroups found for session '{$session}'.");

            return Command::SUCCESS;
        }

        $groupIds = $groups->pluck('id');
        $this->info("Found {$groups->count()} StudentGroup(s): IDs [{$groupIds->implode(', ')}]");

        $eventsQuery = AssessmentEvent::whereIn('group_id', $groupIds);

        if (! $force) {
            $eventsQuery->where(function ($query) {
                $query->whereNull('session')
                    ->orWhere('session', '')
                    ->orWhereNull('year')
                    ->orWhere('year', '')
                    ->orWhereNull('semester')
                    ->orWhere('semester', '');
            });
        }

        $eventsToProcess = $eventsQuery->get();
        $totalEvents = $eventsToProcess->count();

        if ($totalEvents === 0) {
            $this->info('No assessment events with null values found linked to the matching StudentGroup(s).');

            return Command::SUCCESS;
        }

        $this->info("Found {$totalEvents} assessment event(s) with null value(s) to fill.");

        if ($dryRun) {
            $this->info("[DRY RUN] Would fill null fields in {$totalEvents} assessment event(s) with:");
            $this->line("  session:  {$session}");
            $this->line("  year:     {$year}");
            $this->line("  semester: {$semester}");

            return Command::SUCCESS;
        }

        $eventIds = $eventsToProcess->pluck('id');

        if ($force) {
            $updatedCount = AssessmentEvent::whereIn('id', $eventIds)->update([
                'session' => $session,
                'year' => $year,
                'semester' => $semester,
            ]);
            $this->info("Force updated {$updatedCount} assessment event(s).");
        } else {
            $sessionUpdated = AssessmentEvent::whereIn('id', $eventIds)
                ->where(fn ($q) => $q->whereNull('session')->orWhere('session', ''))
                ->update(['session' => $session]);

            $yearUpdated = AssessmentEvent::whereIn('id', $eventIds)
                ->where(fn ($q) => $q->whereNull('year')->orWhere('year', ''))
                ->update(['year' => $year]);

            $semesterUpdated = AssessmentEvent::whereIn('id', $eventIds)
                ->where(fn ($q) => $q->whereNull('semester')->orWhere('semester', ''))
                ->update(['semester' => $semester]);

            $this->info('Successfully filled assessment events with null values:');
            $this->line("  - session  ('{$session}'): {$sessionUpdated} event(s) filled");
            $this->line("  - year     ('{$year}'): {$yearUpdated} event(s) filled");
            $this->line("  - semester ('{$semester}'): {$semesterUpdated} event(s) filled");
        }

        $events = AssessmentEvent::whereIn('id', $eventIds)
            ->with(['teacher', 'course', 'group'])
            ->get();

        $rows = $events->take(50)->map(fn ($event) => [
            $event->id,
            $event->group_id.' ('.($event->group->display_name ?? $event->group->name ?? 'N/A').')',
            $event->course->code ?? 'N/A',
            $event->teacher->name ?? 'N/A',
            $event->session,
            $event->year?->value ?? $event->year ?? 'N/A',
            $event->semester?->value ?? $event->semester ?? 'N/A',
        ]);

        $this->table(
            ['Event ID', 'Group', 'Course', 'Teacher', 'Session', 'Year', 'Semester'],
            $rows
        );

        if ($events->count() > 50) {
            $this->line('... and '.($events->count() - 50).' more events.');
        }

        return Command::SUCCESS;
    }
}
