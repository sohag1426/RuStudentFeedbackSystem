<?php

namespace App\Console\Commands;

use App\Models\AssessmentEvent;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TempUpdateAssessmentEventsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:temp-update-assessment-events
                            {--event= : Specific assessment event ID to process}
                            {--department= : Filter events by department ID}
                            {--dry-run : Simulate the updates without modifying the database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update assessment_events session, year, and semester from student_groups and courses';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $eventId = $this->option('event');
        $departmentId = $this->option('department');

        $this->info('Starting assessment events update...');
        if ($dryRun) {
            $this->warn('DRY RUN MODE ENABLED - No changes will be written to the database.');
        }

        $query = AssessmentEvent::with(['course', 'group']);

        if ($eventId) {
            $query->where('id', $eventId);
        }

        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }

        $events = $query->orderBy('id')->get();
        $totalEvents = $events->count();

        $this->info("Found {$totalEvents} assessment event(s) to inspect.");

        $updatedCount = 0;
        $skippedCount = 0;
        $sessionUpdated = 0;
        $yearUpdated = 0;
        $semesterUpdated = 0;

        $normalize = function ($val): ?string {
            if ($val === null) {
                return null;
            }
            if ($val instanceof \BackedEnum) {
                $val = $val->value;
            }
            $str = trim((string) $val);

            return $str !== '' ? $str : null;
        };

        foreach ($events as $event) {
            $course = $event->course;
            $group = $event->group;

            $sourceSession = $normalize($group?->session);
            $sourceYear = $normalize($course?->year);
            $sourceSemester = $normalize($course?->semester);

            $currentSession = $normalize($event->session);
            $currentYear = $normalize($event->year);
            $currentSemester = $normalize($event->semester);

            $updates = [];
            $changesDescription = [];

            // 1. Session (from student_groups)
            // If source is null -> skip.
            // If identical -> skip.
            // If mismatch or current is null -> update.
            if ($sourceSession !== null) {
                if ($currentSession === null || $currentSession !== $sourceSession) {
                    $updates['session'] = $sourceSession;
                    $changesDescription[] = sprintf('session: %s -> %s', $currentSession ?? 'NULL', $sourceSession);
                    $sessionUpdated++;
                }
            }

            // 2. Year (from courses)
            if ($sourceYear !== null) {
                if ($currentYear === null || $currentYear !== $sourceYear) {
                    $updates['year'] = $sourceYear;
                    $changesDescription[] = sprintf('year: %s -> %s', $currentYear ?? 'NULL', $sourceYear);
                    $yearUpdated++;
                }
            }

            // 3. Semester (from courses)
            if ($sourceSemester !== null) {
                if ($currentSemester === null || $currentSemester !== $sourceSemester) {
                    $updates['semester'] = $sourceSemester;
                    $changesDescription[] = sprintf('semester: %s -> %s', $currentSemester ?? 'NULL', $sourceSemester);
                    $semesterUpdated++;
                }
            }

            if (! empty($updates)) {
                $updatedCount++;
                $this->line(sprintf(
                    '[%s] Event #%d: %s',
                    $dryRun ? 'WOULD UPDATE' : 'UPDATED',
                    $event->id,
                    implode(', ', $changesDescription)
                ));

                if (! $dryRun) {
                    DB::table('assessment_events')
                        ->where('id', $event->id)
                        ->update(array_merge($updates, [
                            'updated_at' => now(),
                        ]));
                }
            } else {
                $skippedCount++;
            }
        }

        $this->newLine();
        $this->info('--- Summary ---');
        $this->line("Total inspected: {$totalEvents}");
        $this->line(($dryRun ? 'Events eligible for update: ' : 'Events updated: ').$updatedCount);
        $this->line("Events skipped (no changes / source null): {$skippedCount}");
        $this->line(" - Session updates: {$sessionUpdated}");
        $this->line(" - Year updates: {$yearUpdated}");
        $this->line(" - Semester updates: {$semesterUpdated}");

        return Command::SUCCESS;
    }
}
