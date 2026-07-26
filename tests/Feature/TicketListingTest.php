<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketListingTest extends TestCase
{
    use RefreshDatabase;

    // Guest harus login sebelum membuka daftar tiket.
    public function test_guest_is_redirected_to_login_from_ticket_list(): void
    {
        $this
            ->get(route('tickets.index'))
            ->assertRedirect(route('login'));
    }

    // Reporter hanya melihat tiket miliknya.
    public function test_reporter_only_sees_own_tickets(): void
    {
        $reporter = User::factory()->create();
        $otherReporter = User::factory()->create();
        $ownTicket = Ticket::factory()->for($reporter, 'reporter')->create();
        $otherTicket = Ticket::factory()->for($otherReporter, 'reporter')->create();

        $this
            ->actingAs($reporter)
            ->get(route('tickets.index'))
            ->assertOk()
            ->assertViewIs('tickets.index')
            ->assertSee($ownTicket->ticket_number)
            ->assertDontSee($otherTicket->ticket_number);
    }

    // Kondisi kosong memberi reporter tautan untuk membuat tiket pertama.
    public function test_empty_ticket_list_is_displayed(): void
    {
        $reporter = User::factory()->create();

        $this
            ->actingAs($reporter)
            ->get(route('tickets.index'))
            ->assertOk()
            ->assertSee('Belum ada tiket')
            ->assertSee(route('tickets.create'));
    }

    // Daftar diurutkan terbaru dan dibatasi sepuluh data per halaman.
    public function test_ticket_list_is_ordered_and_paginated(): void
    {
        $reporter = User::factory()->create();

        Ticket::factory()
            ->count(11)
            ->for($reporter, 'reporter')
            ->sequence(fn ($sequence) => [
                'created_at' => now()->subMinutes(11 - $sequence->index),
                'updated_at' => now()->subMinutes(11 - $sequence->index),
            ])
            ->create();

        $response = $this
            ->actingAs($reporter)
            ->get(route('tickets.index'));

        $tickets = $response->viewData('tickets');
        $actualTimestamps = $tickets->getCollection()
            ->pluck('created_at')
            ->map(fn ($createdAt) => $createdAt->getTimestamp())
            ->all();
        $expectedTimestamps = $actualTimestamps;
        rsort($expectedTimestamps);

        $response->assertOk();
        $this->assertCount(10, $tickets->items());
        $this->assertSame(11, $tickets->total());
        $this->assertSame($expectedTimestamps, $actualTimestamps);
    }
}
