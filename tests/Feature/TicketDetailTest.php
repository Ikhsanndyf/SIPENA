<?php

namespace Tests\Feature;

use App\Models\Attachment;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketDetailTest extends TestCase
{
    use RefreshDatabase;

    // Guest harus login sebelum membuka detail tiket.
    public function test_guest_is_redirected_to_login_from_ticket_detail(): void
    {
        $ticket = Ticket::factory()->create();

        $this
            ->get(route('tickets.show', $ticket))
            ->assertRedirect(route('login'));
    }

    // Reporter dapat melihat informasi lengkap tiket miliknya.
    public function test_reporter_can_view_own_ticket(): void
    {
        $reporter = User::factory()->create();
        $ticket = Ticket::factory()
            ->for($reporter, 'reporter')
            ->create();

        $this
            ->actingAs($reporter)
            ->get(route('tickets.show', $ticket))
            ->assertOk()
            ->assertViewIs('tickets.show')
            ->assertViewHas('ticket', fn (Ticket $viewTicket): bool => $viewTicket->is($ticket))
            ->assertSee($ticket->ticket_number)
            ->assertSee($ticket->title)
            ->assertSee($ticket->application->name);
    }

    // Reporter tidak dapat melihat tiket reporter lain.
    public function test_reporter_cannot_view_another_reporters_ticket(): void
    {
        $reporter = User::factory()->create();
        $otherReporter = User::factory()->create();
        $ticket = Ticket::factory()
            ->for($otherReporter, 'reporter')
            ->create();

        $this
            ->actingAs($reporter)
            ->get(route('tickets.show', $ticket))
            ->assertForbidden();
    }

    // Developer dapat melihat detail seluruh tiket.
    public function test_developer_can_view_ticket_detail(): void
    {
        $developer = User::factory()->developer()->create();
        $ticket = Ticket::factory()->create();

        $this
            ->actingAs($developer)
            ->get(route('tickets.show', $ticket))
            ->assertOk()
            ->assertSee($ticket->ticket_number);
    }

    // Informasi lampiran ditampilkan pada detail tiket.
    public function test_ticket_attachment_is_displayed(): void
    {
        $reporter = User::factory()->create();
        $ticket = Ticket::factory()
            ->for($reporter, 'reporter')
            ->create();
        $attachment = Attachment::factory()
            ->for($ticket)
            ->create();

        $this
            ->actingAs($reporter)
            ->get(route('tickets.show', $ticket))
            ->assertOk()
            ->assertSee($attachment->original_name)
            ->assertSee(asset('storage/'.$attachment->file_path));
    }

    // URL detail menggunakan nomor tiket dan tidak mengekspos ID.
    public function test_ticket_detail_url_uses_ticket_number(): void
    {
        $ticket = Ticket::factory()->create();
        $url = route('tickets.show', $ticket);

        $this->assertStringContainsString(
            '/tickets/'.$ticket->ticket_number,
            $url
        );
        $this->assertStringNotContainsString(
            '/tickets/'.$ticket->id,
            $url
        );
    }

    // Nomor tiket yang tidak tersedia menghasilkan respons tidak ditemukan.
    public function test_missing_ticket_returns_not_found(): void
    {
        $reporter = User::factory()->create();

        $this
            ->actingAs($reporter)
            ->get(route('tickets.show', 'TCK-209912-9999'))
            ->assertNotFound();
    }
}
