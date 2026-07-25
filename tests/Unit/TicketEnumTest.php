<?php

namespace Tests\Unit;

use App\Enums\TicketCategory;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use PHPUnit\Framework\TestCase;

class TicketEnumTest extends TestCase
{
    public function test_ticket_status_contains_expected_values(): void
    {
        $this->assertSame([
            'new',
            'analyzed',
            'in_progress',
            'waiting_confirmation',
            'resolved',
            'rejected',
        ], array_column(TicketStatus::cases(), 'value'));
    }

    public function test_ticket_priority_contains_expected_values(): void
    {
        $this->assertSame([
            'low',
            'medium',
            'high',
            'critical',
        ], array_column(TicketPriority::cases(), 'value'));
    }

    public function test_ticket_category_contains_expected_values(): void
    {
        $this->assertSame([
            'bug',
            'access',
            'data',
            'display',
            'other',
        ], array_column(TicketCategory::cases(), 'value'));
    }
}
