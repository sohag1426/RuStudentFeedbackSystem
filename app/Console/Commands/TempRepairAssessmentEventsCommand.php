<?php

namespace App\Console\Commands;

use App\Models\AssessmentEvent;
use App\Models\StudentGroup;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TempRepairAssessmentEventsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:temp-repair-assessment-events
                            {--session=2024-2025 : The student group session to target, or "all"}
                            {--cutoff=2026-09-01 17:04:54 : Cutoff date/time when columns were introduced}
                            {--all : Process all student groups regardless of session}
                            {--dry-run : Simulate the changes without saving to the database}';

    /**
     * The console command aliases.
     *
     * @var array<string>
     */
    protected $aliases = [
        'temp:repair-assessment-events',
        'app:repair-assessment-events',
        'app:restore-assessment-events',
    ];

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Temp command to repair assessment_events: set null for created_at < cutoff, and sync group values for created_at >= cutoff';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $session = (string) $this->option('session');
        $all = (bool) $this->option('all') || strtolower($session) === 'all';
        $cutoffInput = (string) $this->option('cutoff');
        $dryRun = (bool) $this->option('dry-run');

        try {
            $cutoffDate = Carbon::parse($cutoffInput);
        } catch (\Throwable $e) {
            $this->error("Invalid cutoff date/time format: '{$cutoffInput}'");

            return Command::FAILURE;
        }

        $this->info("Repairing assessment_events with cutoff: {$cutoffDate->toDateTimeString()}");

        $groupIds = null;

        if (! $all) {
            $this->info("Filtering for StudentGroup session: '{$session}'...");
            $groups = StudentGroup::where('session', $session)->get();

            if ($groups->isEmpty()) {
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
            $this->info("Targeting {$groups->count()} StudentGroup(s): IDs [{$groupIds->implode(', ')}]");
        } else {
            $this->info('Targeting all assessment events across all sessions.');
        }

        // ---------------------------------------------------------------------
        // Step 1: Events created BEFORE cutoff -> set session, year, semester = NULL
        // ---------------------------------------------------------------------
        $beforeQuery = AssessmentEvent::where('created_at', '<', $cutoffDate);
        if ($groupIds !== null) {
            $beforeQuery->whereIn('group_id', $groupIds);
        }

        $beforeEvents = $beforeQuery->with(['teacher', 'course', 'group'])->get();
        $beforeCount = $beforeEvents->count();

        $this->line('');
        $this->info("--- STEP 1: Events created BEFORE {$cutoffDate->toDateTimeString()} (to be set to NULL) ---");
        $this->info("Found {$beforeCount} event(s) created before cutoff.");

        if ($beforeCount > 0) {
            $this->table(
                ['Event ID', 'Group ID', 'Course', 'Teacher', 'Created At', 'Current Session', 'Current Year', 'Current Semester'],
                $beforeEvents->take(20)->map(fn ($event) => [
                    $event->id,
                    $event->group_id.' ('.($event->group->name ?? 'N/A').')',
                    $event->course->code ?? 'N/A',
                    $event->teacher->name ?? 'N/A',
                    $event->created_at?->toDateTimeString() ?? 'N/A',
                    $event->session ?? 'NULL',
                    $event->year?->value ?? $event->year ?? 'NULL',
                    $event->semester?->value ?? $event->semester ?? 'NULL',
                ])
            );

            if ($beforeCount > 20) {
                $this->line('... and '.($beforeCount - 20).' more events.');
            }
        }

        // ---------------------------------------------------------------------
        // Step 2: Events created ON OR AFTER cutoff -> sync session, year, semester from StudentGroup
        // ---------------------------------------------------------------------
        $afterQuery = AssessmentEvent::where('created_at', '>=', $cutoffDate);
        if ($groupIds !== null) {
            $afterQuery->whereIn('group_id', $groupIds);
        }

        $afterEvents = $afterQuery->with(['teacher', 'course', 'group'])->get();
        $afterCount = $afterEvents->count();

        $this->line('');
        $this->info("--- STEP 2: Events created ON OR AFTER {$cutoffDate->toDateTimeString()} (to sync from StudentGroup) ---");
        $this->info("Found {$afterCount} event(s) created on/after cutoff.");

        if ($afterCount > 0) {
            $this->table(
                ['Event ID', 'Group ID', 'Course', 'Teacher', 'Created At', 'Sync Session', 'Sync Year', 'Sync Semester'],
                $afterEvents->take(20)->map(function ($event) {
                    $group = $event->group;
                    $groupYear = $group?->year instanceof \App\Enums\Year ? $group->year->value : ($group?->year ?? 'N/A');
                    $groupSem = $group?->semester instanceof \App\Enums\Semester ? $group->semester->value : ($group?->semester ?? 'N/A');

                    return [
                        $event->id,
                        $event->group_id.' ('.($group->name ?? 'N/A').')',
                        $event->course->code ?? 'N/A',
                        $event->teacher->name ?? 'N/A',
                        $event->created_at?->toDateTimeString() ?? 'N/A',
                        $group?->session ?? 'N/A',
                        $groupYear,
                        $groupSem,
                    ];
                })
            );

            if ($afterCount > 20) {
                $this->line('... and '.($afterCount - 20).' more events.');
            }
        }

        // ---------------------------------------------------------------------
        // Execution or Dry Run
        // ---------------------------------------------------------------------
        if ($dryRun) {
            $this->line('');
            $this->info('[DRY RUN] No changes were made to the database.');
            $this->line("Summary: {$beforeCount} event(s) would be set to NULL, {$afterCount} event(s) would be synced from their StudentGroup.");

            return Command::SUCCESS;
        }

        $this->line('');
        $this->info('Applying database changes...');

        // Execute Step 1: Set null for created_at < cutoff
        $beforeUpdatedCount = 0;
        if ($beforeCount > 0) {
            $beforeUpdatedCount = DB::table('assessment_events')
                ->where('created_at', '<', $cutoffDate)
                ->when($groupIds !== null, fn ($q) => $q->whereIn('group_id', $groupIds))
                ->update([
                    'session' => null,
                    'year' => null,
                    'semester' => null,
                ]);
        }

        // Execute Step 2: Sync from group for created_at >= cutoff
        $afterUpdatedCount = 0;
        if ($afterCount > 0) {
            foreach ($afterEvents as $event) {
                $group = $event->group;
                if ($group) {
                    $yearVal = $group->year instanceof \App\Enums\Year ? $group->year->value : $group->year;
                    $semesterVal = $group->semester instanceof \App\Enums\Semester ? $group->semester->value : $group->semester;

                    DB::table('assessment_events')
                        ->where('id', $event->id)
                        ->update([
                            'session' => $group->session,
                            'year' => $yearVal,
                            'semester' => $semesterVal,
                        ]);
                    $afterUpdatedCount++;
                }
            }
        }

        $this->info('Repair completed successfully!');
        $this->line("  - Step 1 (created_at < {$cutoffDate->toDateTimeString()}): {$beforeUpdatedCount} event(s) set to NULL");
        $this->line("  - Step 2 (created_at >= {$cutoffDate->toDateTimeString()}): {$afterUpdatedCount} event(s) synced from their StudentGroup");

        return Command::SUCCESS;
    }
}
