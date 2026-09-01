<?php

namespace App\Services;

class SessionService
{
    /**
     * Default number of sessions to generate.
     */
    public const DEFAULT_COUNT = 6;

    /**
     * Get the list of academic sessions.
     *
     * Format: {start_year}-{end_year}, descending from the current year.
     * e.g. For 2026: ['2026-2027', '2025-2026', '2024-2025', '2023-2024', '2022-2023', '2021-2022']
     *
     * @param int $count Number of sessions to generate (default: 6)
     * @param int|null $currentYear Base year (defaults to current year)
     * @return array<string>
     */
    public static function getSessions(int $count = self::DEFAULT_COUNT, ?int $currentYear = null): array
    {
        $year = $currentYear ?? (int) now()->format('Y');
        $sessions = [];

        for ($i = 0; $i < $count; $i++) {
            $startYear = $year - $i;
            $endYear = $startYear + 1;
            $sessions[] = "{$startYear}-{$endYear}";
        }

        return $sessions;
    }

    /**
     * Get dropdown options for sessions, ensuring an existing session (if any) is included.
     *
     * @param string|null $selectedSession
     * @param int $count
     * @param int|null $currentYear
     * @return array<string>
     */
    public static function getDropdownSessions(?string $selectedSession = null, int $count = self::DEFAULT_COUNT, ?int $currentYear = null): array
    {
        $sessions = static::getSessions($count, $currentYear);

        if ($selectedSession && ! in_array($selectedSession, $sessions, true)) {
            $sessions[] = $selectedSession;
        }

        return $sessions;
    }

    /**
     * Instance wrapper for getSessions.
     *
     * @param int $count
     * @param int|null $currentYear
     * @return array<string>
     */
    public function sessions(int $count = self::DEFAULT_COUNT, ?int $currentYear = null): array
    {
        return static::getSessions($count, $currentYear);
    }
}
