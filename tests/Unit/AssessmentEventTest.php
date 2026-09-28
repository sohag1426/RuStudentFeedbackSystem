<?php

namespace Tests\Unit;

use App\Models\AssessmentEvent;
use Tests\TestCase;

class AssessmentEventTest extends TestCase
{
    public function test_stop_time_attribute_is_formatted_in_human_readable_format()
    {
        $event = new AssessmentEvent;
        $event->setRawAttributes([
            'stop_time' => '2026-09-30 18:45:00',
        ], true);

        $this->assertSame('30 Sep 2026', $event->stop_time);
        $this->assertSame('2026-09-30 18:45:00', $event->getRawOriginal('stop_time'));
    }

    public function test_stop_time_attribute_returns_null_when_empty()
    {
        $event = new AssessmentEvent;
        $this->assertNull($event->stop_time);
    }

    public function test_start_time_attribute_is_formatted_in_human_readable_format()
    {
        $event = new AssessmentEvent;
        $event->setRawAttributes([
            'start_time' => '2026-09-28 10:00:00',
        ], true);

        $this->assertSame('28 Sep 2026', $event->start_time);
        $this->assertSame('2026-09-28 10:00:00', $event->getRawOriginal('start_time'));
    }

    public function test_created_at_attribute_is_formatted_in_human_readable_format()
    {
        $event = new AssessmentEvent;
        $event->setRawAttributes([
            'created_at' => '2026-09-01 12:00:00',
        ], true);

        $this->assertSame('01 Sep 2026', $event->created_at);
        $this->assertSame('2026-09-01 12:00:00', $event->getRawOriginal('created_at'));
    }
}
