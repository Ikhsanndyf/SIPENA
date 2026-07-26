<?php

namespace Tests\Feature;

use App\Enums\TicketStatus;
use App\Exceptions\InvalidTicketStatusTransition;
use App\Models\Ticket;
use App\Models\User;
use App\Services\TicketStatusService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketStatusWorkflowTest extends TestCase
{
    use RefreshDatabase;

    // Developer menjalankan transisi legal dan menghasilkan history.
    public function test_developer_can_apply_legal_status_transition(): void
    {
        $developer = User::factory()->developer()->create();
        $ticket = Ticket::factory()->create(['status' => TicketStatus::New]);

        $this
            ->actingAs($developer)
            ->patch(route('developer.tickets.status', $ticket), [
                'status' => TicketStatus::Analyzed->value,
                'notes' => 'Tiket telah dianalisis.',
            ])
            ->assertRedirect(route('developer.tickets.show', $ticket))
            ->assertSessionHas('success');

        $ticket->refresh();
        $history = $ticket->statusHistories()->sole();

        $this->assertSame(TicketStatus::Analyzed, $ticket->status);
        $this->assertSame(TicketStatus::New, $history->from_status);
        $this->assertSame(TicketStatus::Analyzed, $history->to_status);
        $this->assertSame($developer->id, $history->changed_by);
        $this->assertSame('Tiket telah dianalisis.', $history->notes);
    }

    // Transisi yang melompati workflow ditolak tanpa mengubah database.
    public function test_illegal_status_transition_is_rejected(): void
    {
        $developer = User::factory()->developer()->create();
        $ticket = Ticket::factory()->create(['status' => TicketStatus::New]);

        $this
            ->actingAs($developer)
            ->from(route('developer.tickets.show', $ticket))
            ->patch(route('developer.tickets.status', $ticket), [
                'status' => TicketStatus::InProgress->value,
            ])
            ->assertRedirect(route('developer.tickets.show', $ticket))
            ->assertSessionHasErrors('status');

        $this->assertSame(TicketStatus::New, $ticket->fresh()->status);
        $this->assertDatabaseCount('ticket_status_histories', 0);
    }

    // Solusi wajib tersedia sebelum status menunggu konfirmasi.
    public function test_resolution_is_required_before_waiting_confirmation(): void
    {
        $developer = User::factory()->developer()->create();
        $ticket = Ticket::factory()->create([
            'status' => TicketStatus::InProgress,
            'resolution_notes' => null,
        ]);

        $this
            ->actingAs($developer)
            ->patch(route('developer.tickets.status', $ticket), [
                'status' => TicketStatus::WaitingConfirmation->value,
            ])
            ->assertSessionHasErrors('status');

        $this->assertSame(TicketStatus::InProgress, $ticket->fresh()->status);
    }

    // Developer dapat mengirim solusi untuk dikonfirmasi reporter.
    public function test_developer_can_move_solved_ticket_to_waiting_confirmation(): void
    {
        $developer = User::factory()->developer()->create();
        $ticket = Ticket::factory()->create([
            'status' => TicketStatus::InProgress,
            'resolution_notes' => 'Konfigurasi koneksi telah diperbaiki.',
        ]);

        $this
            ->actingAs($developer)
            ->patch(route('developer.tickets.status', $ticket), [
                'status' => TicketStatus::WaitingConfirmation->value,
                'notes' => 'Menunggu pengujian dari reporter.',
            ])
            ->assertRedirect(route('developer.tickets.show', $ticket));

        $this->assertSame(
            TicketStatus::WaitingConfirmation,
            $ticket->fresh()->status,
        );
    }

    // Developer dapat membuka kembali tiket yang menunggu konfirmasi.
    public function test_developer_can_reopen_waiting_confirmation_ticket(): void
    {
        $developer = User::factory()->developer()->create();
        $ticket = Ticket::factory()->create([
            'status' => TicketStatus::WaitingConfirmation,
        ]);

        app(TicketStatusService::class)->transitionByDeveloper(
            $ticket,
            $developer,
            TicketStatus::InProgress,
            'Reporter meminta pemeriksaan ulang.',
        );

        $this->assertSame(TicketStatus::InProgress, $ticket->fresh()->status);
    }

    // Developer tidak boleh menetapkan resolved secara langsung.
    public function test_developer_cannot_resolve_ticket_directly(): void
    {
        $developer = User::factory()->developer()->create();
        $ticket = Ticket::factory()->create([
            'status' => TicketStatus::WaitingConfirmation,
        ]);

        $this->expectException(InvalidTicketStatusTransition::class);

        app(TicketStatusService::class)->transitionByDeveloper(
            $ticket,
            $developer,
            TicketStatus::Resolved,
        );
    }

    // Reporter pemilik mengonfirmasi tiket dan mengisi resolved_at.
    public function test_owner_confirmation_resolves_ticket(): void
    {
        $reporter = User::factory()->create();
        $ticket = Ticket::factory()
            ->for($reporter, 'reporter')
            ->create(['status' => TicketStatus::WaitingConfirmation]);

        app(TicketStatusService::class)->confirmByReporter($ticket, $reporter);

        $ticket->refresh();
        $history = $ticket->statusHistories()->sole();

        $this->assertSame(TicketStatus::Resolved, $ticket->status);
        $this->assertNotNull($ticket->resolved_at);
        $this->assertSame(TicketStatus::Resolved, $history->to_status);
        $this->assertSame($reporter->id, $history->changed_by);
    }

    // Reporter lain tidak dapat mengonfirmasi tiket.
    public function test_other_reporter_cannot_confirm_ticket(): void
    {
        $owner = User::factory()->create();
        $otherReporter = User::factory()->create();
        $ticket = Ticket::factory()
            ->for($owner, 'reporter')
            ->create(['status' => TicketStatus::WaitingConfirmation]);

        $this->expectException(AuthorizationException::class);

        app(TicketStatusService::class)->confirmByReporter(
            $ticket,
            $otherReporter,
        );
    }
}
