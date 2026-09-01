<?php

namespace Tests\Unit;

use App\Services\SessionService;
use PHPUnit\Framework\TestCase;

class SessionServiceTest extends TestCase
{
    public function test_get_sessions_returns_six_sessions_by_default()
    {
        $sessions = SessionService::getSessions();

        $this->assertIsArray($sessions);
        $this->assertCount(6, $sessions);
    }

    public function test_get_sessions_generates_expected_format_for_a_given_year()
    {
        $sessions = SessionService::getSessions(6, 2026);

        $expected = [
            '2026-2027',
            '2025-2026',
            '2024-2025',
            '2023-2024',
            '2022-2023',
            '2021-2022',
        ];

        $this->assertSame($expected, $sessions);
    }

    public function test_get_sessions_with_custom_count()
    {
        $sessions = SessionService::getSessions(3, 2026);

        $expected = [
            '2026-2027',
            '2025-2026',
            '2024-2025',
        ];

        $this->assertSame($expected, $sessions);
    }

    public function test_get_dropdown_sessions_includes_selected_session_if_not_in_list()
    {
        $sessions = SessionService::getDropdownSessions('2018-2019', 6, 2026);

        $this->assertCount(7, $sessions);
        $this->assertContains('2018-2019', $sessions);
    }

    public function test_get_dropdown_sessions_does_not_duplicate_already_present_session()
    {
        $sessions = SessionService::getDropdownSessions('2025-2026', 6, 2026);

        $this->assertCount(6, $sessions);
    }

    public function test_instance_method_works_identically()
    {
        $service = new SessionService();
        $sessions = $service->sessions(6, 2026);

        $this->assertSame(SessionService::getSessions(6, 2026), $sessions);
    }
}
