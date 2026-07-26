<?php

namespace Tests\Feature;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeveloperTicketHandlingTest extends TestCase
{
    use RefreshDatabase;

    // Developer dapat menentukan PIC, prioritas, analisis, dan solusi.
    public function test_developer_can_update_ticket_handling(): void
    {
        $developer = User::factory()->developer()->create();
        $assignee = User::factory()->developer()->create();
        $ticket = Ticket::factory()->create();

        $this
            ->actingAs($developer)
            ->patch(route('developer.tickets.handling', $ticket), [
                'assigned_to' => $assignee->id,
                'priority' => TicketPriority::Critical->value,
                'analysis_notes' => 'Terjadi kegagalan pada validasi payload.',
                'resolution_notes' => 'Perbaiki format payload sebelum dikirim.',
            ])
            ->assertRedirect(route('developer.tickets.show', $ticket))
            ->assertSessionHas('success');

        $ticket->refresh();

        $this->assertSame($assignee->id, $ticket->assigned_to);
        $this->assertSame(TicketPriority::Critical, $ticket->priority);
        $this->assertSame(
            'Terjadi kegagalan pada validasi payload.',
            $ticket->analysis_notes,
        );
        $this->assertSame(
            'Perbaiki format payload sebelum dikirim.',
            $ticket->resolution_notes,
        );
    }

    // PIC tidak boleh menunjuk pengguna dengan role reporter.
    public function test_assignee_must_be_a_developer(): void
    {
        $developer = User::factory()->developer()->create();
        $reporter = User::factory()->create();
        $ticket = Ticket::factory()->create();

        $this
            ->actingAs($developer)
            ->patch(route('developer.tickets.handling', $ticket), [
                'assigned_to' => $reporter->id,
                'priority' => TicketPriority::High->value,
            ])
            ->assertSessionHasErrors('assigned_to');

        $this->assertNull($ticket->fresh()->assigned_to);
    }

    // Field di luar penanganan tidak dapat dimanipulasi melalui request.
    public function test_handling_request_ignores_system_and_reporter_fields(): void
    {
        $developer = User::factory()->developer()->create();
        $ticket = Ticket::factory()->create(['status' => TicketStatus::New]);
        $originalTitle = $ticket->title;

        $this
            ->actingAs($developer)
            ->patch(route('developer.tickets.handling', $ticket), [
                'assigned_to' => $developer->id,
                'priority' => TicketPriority::High->value,
                'status' => TicketStatus::Resolved->value,
                'title' => 'Judul yang dimanipulasi',
                'reporter_id' => $developer->id,
            ])
            ->assertRedirect(route('developer.tickets.show', $ticket));

        $ticket->refresh();

        $this->assertSame(TicketStatus::New, $ticket->status);
        $this->assertSame($originalTitle, $ticket->title);
        $this->assertNotSame($developer->id, $ticket->reporter_id);
    }

    // Reporter tidak dapat mengakses endpoint penanganan developer.
    public function test_reporter_cannot_update_ticket_handling(): void
    {
        $reporter = User::factory()->create();
        $ticket = Ticket::factory()
            ->for($reporter, 'reporter')
            ->create();

        $this
            ->actingAs($reporter)
            ->patch(route('developer.tickets.handling', $ticket), [
                'priority' => TicketPriority::Critical->value,
            ])
            ->assertForbidden();

        $this->assertSame(TicketPriority::Medium, $ticket->fresh()->priority);
    }
}
