<?php

namespace Tests\Feature;

use App\Enums\TicketStatus;
use App\Models\Attachment;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TicketDeletionTest extends TestCase
{
    use RefreshDatabase;

    // Guest harus login sebelum mencoba menghapus tiket.
    public function test_guest_is_redirected_to_login_when_deleting_ticket(): void
    {
        $ticket = Ticket::factory()->create();

        $this
            ->delete(route('tickets.destroy', $ticket))
            ->assertRedirect(route('login'));

        $this->assertDatabaseHas('tickets', ['id' => $ticket->id]);
    }

    // Reporter pemilik dapat menghapus tiket yang masih new.
    public function test_owner_can_delete_own_new_ticket(): void
    {
        $reporter = User::factory()->create();
        $ticket = Ticket::factory()
            ->for($reporter, 'reporter')
            ->create(['status' => TicketStatus::New]);

        $this
            ->actingAs($reporter)
            ->delete(route('tickets.destroy', $ticket))
            ->assertRedirect(route('tickets.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('tickets', ['id' => $ticket->id]);
    }

    // Reporter lain tidak dapat menghapus tiket yang bukan miliknya.
    public function test_other_reporter_cannot_delete_ticket(): void
    {
        $owner = User::factory()->create();
        $otherReporter = User::factory()->create();
        $ticket = Ticket::factory()
            ->for($owner, 'reporter')
            ->create(['status' => TicketStatus::New]);

        $this
            ->actingAs($otherReporter)
            ->delete(route('tickets.destroy', $ticket))
            ->assertForbidden();

        $this->assertDatabaseHas('tickets', ['id' => $ticket->id]);
    }

    // Reporter tidak dapat menghapus tiket yang sudah diproses.
    public function test_owner_cannot_delete_processed_ticket(): void
    {
        $reporter = User::factory()->create();
        $ticket = Ticket::factory()
            ->for($reporter, 'reporter')
            ->create(['status' => TicketStatus::InProgress]);

        $this
            ->actingAs($reporter)
            ->delete(route('tickets.destroy', $ticket))
            ->assertForbidden();

        $this->assertDatabaseHas('tickets', ['id' => $ticket->id]);
    }

    // Developer tidak dapat menghapus laporan milik reporter.
    public function test_developer_cannot_delete_reporter_ticket(): void
    {
        $developer = User::factory()->developer()->create();
        $ticket = Ticket::factory()->create(['status' => TicketStatus::New]);

        $this
            ->actingAs($developer)
            ->delete(route('tickets.destroy', $ticket))
            ->assertForbidden();

        $this->assertDatabaseHas('tickets', ['id' => $ticket->id]);
    }

    // Penghapusan tiket turut menghapus metadata dan file lampiran.
    public function test_deleting_ticket_removes_attachment_record_and_file(): void
    {
        Storage::fake('public');

        $reporter = User::factory()->create();
        $ticket = Ticket::factory()
            ->for($reporter, 'reporter')
            ->create(['status' => TicketStatus::New]);
        $attachment = Attachment::factory()
            ->for($ticket)
            ->create(['file_path' => 'attachments/to-delete.png']);
        Storage::disk('public')->put($attachment->file_path, 'attachment-file');

        $this
            ->actingAs($reporter)
            ->delete(route('tickets.destroy', $ticket))
            ->assertRedirect(route('tickets.index'));

        $this->assertDatabaseMissing('tickets', ['id' => $ticket->id]);
        $this->assertDatabaseMissing('attachments', ['id' => $attachment->id]);
        Storage::disk('public')->assertMissing($attachment->file_path);
    }
}
