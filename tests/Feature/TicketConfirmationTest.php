<?php

namespace Tests\Feature;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketConfirmationTest extends TestCase
{
    use RefreshDatabase;

    // Reporter pemilik dapat melihat solusi dan mengonfirmasi tiket.
    public function test_owner_can_confirm_waiting_confirmation_ticket(): void
    {
        $reporter = User::factory()->create();
        $ticket = Ticket::factory()
            ->for($reporter, 'reporter')
            ->create([
                'status' => TicketStatus::WaitingConfirmation,
                'analysis_notes' => 'Konfigurasi koneksi tidak sesuai.',
                'resolution_notes' => 'Konfigurasi koneksi telah diperbaiki.',
            ]);

        $this
            ->actingAs($reporter)
            ->get(route('tickets.show', $ticket))
            ->assertOk()
            ->assertSee('Konfigurasi koneksi tidak sesuai.')
            ->assertSee('Konfigurasi koneksi telah diperbaiki.')
            ->assertSee('Konfirmasi Selesai');

        $this
            ->actingAs($reporter)
            ->patch(route('tickets.confirm', $ticket))
            ->assertRedirect(route('tickets.show', $ticket))
            ->assertSessionHas('success');

        $ticket->refresh();

        $this->assertSame(TicketStatus::Resolved, $ticket->status);
        $this->assertNotNull($ticket->resolved_at);
    }

    // Reporter lain tidak dapat mengonfirmasi tiket.
    public function test_other_reporter_cannot_confirm_ticket(): void
    {
        $owner = User::factory()->create();
        $otherReporter = User::factory()->create();
        $ticket = Ticket::factory()
            ->for($owner, 'reporter')
            ->create(['status' => TicketStatus::WaitingConfirmation]);

        $this
            ->actingAs($otherReporter)
            ->patch(route('tickets.confirm', $ticket))
            ->assertForbidden();

        $this->assertSame(
            TicketStatus::WaitingConfirmation,
            $ticket->fresh()->status,
        );
    }

    // Tiket di luar waiting confirmation tidak dapat dikonfirmasi.
    public function test_owner_cannot_confirm_ticket_in_another_status(): void
    {
        $reporter = User::factory()->create();
        $ticket = Ticket::factory()
            ->for($reporter, 'reporter')
            ->create(['status' => TicketStatus::InProgress]);

        $this
            ->actingAs($reporter)
            ->patch(route('tickets.confirm', $ticket))
            ->assertForbidden();

        $this->assertSame(TicketStatus::InProgress, $ticket->fresh()->status);
    }
}
