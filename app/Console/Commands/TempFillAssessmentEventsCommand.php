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
    protected $description = 'Temp command to fill session, year, and semester on assessment_events for student groups of session 2024-2025';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $session = (string) $this->option('session');
        $year = (string) $this->option('year');
        $semester = (string) $this->option('semester');
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
        $totalEvents = $eventsQuery->count();

        if ($totalEvents === 0) {
            $this->warn('No assessment events found linked to the matching StudentGroup(s).');

            return Command::SUCCESS;
        }

        $this->info("Found {$totalEvents} assessment event(s) to update.");

        if ($dryRun) {
            $this->info("[DRY RUN] Would update {$totalEvents} assessment event(s) with:");
            $this->line("  session:  {$session}");
            $this->line("  year:     {$year}");
            $this->line("  semester: {$semester}");

            return Command::SUCCESS;
        }

        $updatedCount = AssessmentEvent::whereIn('group_id', $groupIds)->update([
            'session' => $session,
            'year' => $year,
            'semester' => $semester,
        ]);

        $this->info("Successfully updated {$updatedCount} assessment event(s):");
        $this->line("  - session  => '{$session}'");
        $this->line("  - year     => '{$year}'");
        $this->line("  - semester => '{$semester}'");

        $events = AssessmentEvent::whereIn('group_id', $groupIds)
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
