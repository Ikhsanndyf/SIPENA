<?php

namespace Tests\Feature;

use App\Enums\TicketCategory;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Application;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_ticket_has_enum_casts_and_relationships(): void
    {
        $reporter = User::factory()->create();
        $developer = User::factory()->developer()->create();
        $application = Application::factory()->create();

        $ticket = Ticket::factory()->create([
            'reporter_id' => $reporter,
            'assigned_to' => $developer,
            'application_id' => $application,
            'category' => TicketCategory::Data,
            'priority' => TicketPriority::High,
            'status' => TicketStatus::Analyzed,
        ]);

        $this->assertSame(TicketCategory::Data, $ticket->category);
        $this->assertSame(TicketPriority::High, $ticket->priority);
        $this->assertSame(TicketStatus::Analyzed, $ticket->status);
        $this->assertTrue($ticket->reporter->is($reporter));
        $this->assertTrue($ticket->assignee->is($developer));
        $this->assertTrue($ticket->application->is($application));
        $this->assertTrue($reporter->reportedTickets->contains($ticket));
        $this->assertTrue($developer->assignedTickets->contains($ticket));
        $this->assertTrue($application->tickets->contains($ticket));
    }

    public function test_ticket_uses_new_status_and_medium_priority_by_default(): void
    {
        $ticket = Ticket::create([
            'reporter_id' => User::factory()->create()->id,
            'application_id' => Application::factory()->create()->id,
            'title' => 'Data gagal tersimpan',
            'category' => TicketCategory::Data,
            'description' => 'Data tidak tersimpan setelah tombol simpan ditekan.',
        ]);

        $ticket->refresh();

        $this->assertSame(TicketStatus::New, $ticket->status);
        $this->assertSame(TicketPriority::Medium, $ticket->priority);
    }
}
