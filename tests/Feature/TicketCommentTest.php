<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TicketCommentTest extends TestCase
{
    use RefreshDatabase;

    // Guest harus login sebelum mengirim komentar.
    public function test_guest_cannot_comment_on_ticket(): void
    {
        $ticket = Ticket::factory()->create();

        $this
            ->post(route('tickets.comments.store', $ticket), [
                'body' => 'Komentar guest.',
            ])
            ->assertRedirect(route('login'));

        $this->assertDatabaseCount('ticket_comments', 0);
    }

    // Reporter pemilik dapat menambahkan komentar pada tiketnya.
    public function test_owner_reporter_can_comment_on_ticket(): void
    {
        $reporter = User::factory()->create();
        $ticket = Ticket::factory()
            ->for($reporter, 'reporter')
            ->create();

        $this
            ->actingAs($reporter)
            ->from(route('tickets.show', $ticket))
            ->post(route('tickets.comments.store', $ticket), [
                'body' => 'Kendala masih terjadi setelah cache dibersihkan.',
            ])
            ->assertRedirect(route('tickets.show', $ticket))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('ticket_comments', [
            'ticket_id' => $ticket->id,
            'user_id' => $reporter->id,
            'body' => 'Kendala masih terjadi setelah cache dibersihkan.',
        ]);
    }

    // Reporter lain tidak dapat membaca konteks atau mengomentari tiket.
    public function test_other_reporter_cannot_comment_on_ticket(): void
    {
        $owner = User::factory()->create();
        $otherReporter = User::factory()->create();
        $ticket = Ticket::factory()
            ->for($owner, 'reporter')
            ->create();

        $this
            ->actingAs($otherReporter)
            ->post(route('tickets.comments.store', $ticket), [
                'body' => 'Komentar tanpa hak akses.',
            ])
            ->assertForbidden();

        $this->assertDatabaseCount('ticket_comments', 0);
    }

    // Developer dapat memberi komentar melalui area operasional.
    public function test_developer_can_comment_on_any_ticket(): void
    {
        $developer = User::factory()->developer()->create();
        $ticket = Ticket::factory()->create();

        $this
            ->actingAs($developer)
            ->from(route('developer.tickets.show', $ticket))
            ->post(route('developer.tickets.comments.store', $ticket), [
                'body' => 'Mohon kirimkan langkah reproduksi yang lebih rinci.',
            ])
            ->assertRedirect(route('developer.tickets.show', $ticket))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('ticket_comments', [
            'ticket_id' => $ticket->id,
            'user_id' => $developer->id,
        ]);
    }

    // Isi komentar wajib memenuhi panjang 2 sampai 2000 karakter.
    public function test_comment_body_is_validated(): void
    {
        $reporter = User::factory()->create();
        $ticket = Ticket::factory()
            ->for($reporter, 'reporter')
            ->create();

        $this
            ->actingAs($reporter)
            ->post(route('tickets.comments.store', $ticket), ['body' => 'A'])
            ->assertSessionHasErrors('body');

        $this
            ->actingAs($reporter)
            ->post(route('tickets.comments.store', $ticket), [
                'body' => str_repeat('A', 2001),
            ])
            ->assertSessionHasErrors('body');

        $this->assertDatabaseCount('ticket_comments', 0);
    }

    // Komentar ditampilkan aman, berurutan, dan dipaginasi sepuluh data.
    public function test_comments_are_escaped_ordered_and_paginated(): void
    {
        $reporter = User::factory()->create();
        $ticket = Ticket::factory()
            ->for($reporter, 'reporter')
            ->create();

        TicketComment::factory()
            ->count(11)
            ->for($ticket)
            ->for($reporter)
            ->sequence(fn ($sequence) => [
                'body' => $sequence->index === 0
                    ? '<script>alert("aman")</script>'
                    : "Komentar urutan {$sequence->index}",
                'created_at' => now()->addMinutes($sequence->index),
                'updated_at' => now()->addMinutes($sequence->index),
            ])
            ->create();

        DB::flushQueryLog();
        DB::enableQueryLog();

        $response = $this
            ->actingAs($reporter)
            ->get(route('tickets.show', $ticket));

        $queryCount = count(DB::getQueryLog());
        DB::disableQueryLog();
        $comments = $response->viewData('comments');

        $response
            ->assertOk()
            ->assertSee('&lt;script&gt;', false)
            ->assertDontSee('<script>', false);
        $this->assertCount(10, $comments->items());
        $this->assertSame(11, $comments->total());
        $this->assertSame(
            '<script>alert("aman")</script>',
            $comments->items()[0]->body,
        );
        $this->assertSame(
            'Komentar urutan 9',
            $comments->items()[9]->body,
        );
        $this->assertLessThanOrEqual(
            10,
            $queryCount,
            "Detail dengan komentar menjalankan {$queryCount} query.",
        );
    }

    // Metadata komentar ikut terhapus ketika tiket dihapus.
    public function test_comments_are_deleted_with_ticket(): void
    {
        $reporter = User::factory()->create();
        $ticket = Ticket::factory()
            ->for($reporter, 'reporter')
            ->create();
        $comment = TicketComment::factory()
            ->for($ticket)
            ->for($reporter)
            ->create();

        $this
            ->actingAs($reporter)
            ->delete(route('tickets.destroy', $ticket))
            ->assertRedirect(route('tickets.index'));

        $this->assertDatabaseMissing('ticket_comments', [
            'id' => $comment->id,
        ]);
    }
}
