<?php

namespace Tests\Feature;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    // Reporter melihat tiket miliknya sendiri.
    public function test_reporter_can_view_own_ticket(): void
    {
        $reporter = User::factory()->create();
        $ticket = Ticket::factory()->for($reporter, 'reporter')->create();

        $this->assertTrue($reporter->can('view', $ticket));
    }

    // Reporter tidak dapat melihat tiket reporter lain.
    public function test_reporter_cannot_view_another_reporters_ticket(): void
    {
        $reporter = User::factory()->create();
        $ticket = Ticket::factory()->create();

        $this->assertFalse($reporter->can('view', $ticket));
    }

    // Developer dapat melihat dan menangani semua tiket.
    public function test_developer_can_view_and_handle_ticket(): void
    {
        $developer = User::factory()->developer()->create();
        $ticket = Ticket::factory()->create();

        $this->assertTrue($developer->can('view', $ticket));
        $this->assertTrue($developer->can('handle', $ticket));
    }

    // Reporter hanya mengubah dan menghapus tiket new miliknya.
    public function test_reporter_can_only_update_and_delete_own_new_ticket(): void
    {
        $reporter = User::factory()->create();

        $newTicket = Ticket::factory()
            ->for($reporter, 'reporter')
            ->create(['status' => TicketStatus::New]);

        $analyzedTicket = Ticket::factory()
            ->for($reporter, 'reporter')
            ->create(['status' => TicketStatus::Analyzed]);

        $this->assertTrue($reporter->can('update', $newTicket));
        $this->assertTrue($reporter->can('delete', $newTicket));
        $this->assertFalse($reporter->can('update', $analyzedTicket));
        $this->assertFalse($reporter->can('delete', $analyzedTicket));
    }

    // Hanya reporter pemilik yang mengonfirmasi tiket.
    public function test_only_owner_can_confirm_waiting_confirmation_ticket(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();

        $ticket = Ticket::factory()
            ->for($owner, 'reporter')
            ->create([
                'status' => TicketStatus::WaitingConfirmation,
            ]);

        $this->assertTrue($owner->can('confirm', $ticket));
        $this->assertFalse($other->can('confirm', $ticket));
    }
}
