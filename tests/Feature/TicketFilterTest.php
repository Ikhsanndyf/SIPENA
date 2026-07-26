<?php

namespace Tests\Feature;

use App\Enums\TicketCategory;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Application;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TicketFilterTest extends TestCase
{
    use RefreshDatabase;

    // Reporter dapat menggabungkan seluruh filter tanpa melihat tiket orang lain.
    public function test_reporter_can_apply_combined_ticket_filters(): void
    {
        $reporter = User::factory()->create();
        $otherReporter = User::factory()->create();
        $developer = User::factory()->developer()->create();
        $application = Application::factory()->create();
        $createdAt = now()->subDays(2);

        $matchingTicket = Ticket::factory()
            ->for($reporter, 'reporter')
            ->create([
                'application_id' => $application->id,
                'assigned_to' => $developer->id,
                'title' => 'Sinkronisasi transaksi gagal',
                'category' => TicketCategory::Data,
                'priority' => TicketPriority::High,
                'status' => TicketStatus::InProgress,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        $otherOwnTicket = Ticket::factory()
            ->for($reporter, 'reporter')
            ->create(['title' => 'Tiket reporter yang tidak cocok']);
        $otherReporterTicket = Ticket::factory()
            ->for($otherReporter, 'reporter')
            ->create(['title' => 'Sinkronisasi milik pengguna lain']);

        $this
            ->actingAs($reporter)
            ->get(route('tickets.index', [
                'search' => 'Sinkronisasi',
                'application_id' => $application->id,
                'status' => TicketStatus::InProgress->value,
                'priority' => TicketPriority::High->value,
                'category' => TicketCategory::Data->value,
                'assigned_to' => $developer->id,
                'date_from' => $createdAt->copy()->subDay()->toDateString(),
                'date_to' => $createdAt->copy()->addDay()->toDateString(),
            ]))
            ->assertOk()
            ->assertSee($matchingTicket->ticket_number)
            ->assertDontSee($otherOwnTicket->ticket_number)
            ->assertDontSee($otherReporterTicket->ticket_number);
    }

    // Filter khusus unassigned hanya menampilkan tiket tanpa PIC.
    public function test_unassigned_filter_only_displays_tickets_without_pic(): void
    {
        $reporter = User::factory()->create();
        $developer = User::factory()->developer()->create();
        $unassignedTicket = Ticket::factory()
            ->for($reporter, 'reporter')
            ->create(['assigned_to' => null]);
        $assignedTicket = Ticket::factory()
            ->for($reporter, 'reporter')
            ->create(['assigned_to' => $developer->id]);

        $this
            ->actingAs($reporter)
            ->get(route('tickets.index', ['assigned_to' => 'unassigned']))
            ->assertOk()
            ->assertSee($unassignedTicket->ticket_number)
            ->assertDontSee($assignedTicket->ticket_number);
    }

    // Developer dapat mencari tiket berdasarkan nama reporter dan tanggal.
    public function test_developer_can_search_reporter_and_filter_date_range(): void
    {
        $developer = User::factory()->developer()->create();
        $reporter = User::factory()->create(['name' => 'Reporter Khusus']);
        $insideDate = now()->subDays(3);
        $outsideDate = now()->subDays(20);
        $matchingTicket = Ticket::factory()
            ->for($reporter, 'reporter')
            ->create([
                'created_at' => $insideDate,
                'updated_at' => $insideDate,
            ]);
        $outsideTicket = Ticket::factory()
            ->for($reporter, 'reporter')
            ->create([
                'created_at' => $outsideDate,
                'updated_at' => $outsideDate,
            ]);

        $this
            ->actingAs($developer)
            ->get(route('developer.tickets.index', [
                'search' => 'Reporter Khusus',
                'date_from' => now()->subDays(5)->toDateString(),
                'date_to' => now()->toDateString(),
            ]))
            ->assertOk()
            ->assertSee($matchingTicket->ticket_number)
            ->assertDontSee($outsideTicket->ticket_number);
    }

    // Rentang tanggal terbalik dan PIC reporter ditolak oleh Form Request.
    public function test_invalid_filter_values_are_rejected(): void
    {
        $reporter = User::factory()->create();

        $this
            ->actingAs($reporter)
            ->get(route('tickets.index', [
                'assigned_to' => $reporter->id,
                'date_from' => now()->toDateString(),
                'date_to' => now()->subDay()->toDateString(),
            ]))
            ->assertSessionHasErrors(['assigned_to', 'date_to']);
    }

    // Tanggal awal atau akhir dapat digunakan sendiri.
    public function test_each_date_filter_can_be_used_independently(): void
    {
        $reporter = User::factory()->create();
        $recentTicket = Ticket::factory()
            ->for($reporter, 'reporter')
            ->create(['created_at' => now()->subDay()]);
        $oldTicket = Ticket::factory()
            ->for($reporter, 'reporter')
            ->create(['created_at' => now()->subMonth()]);

        $this
            ->actingAs($reporter)
            ->get(route('tickets.index', [
                'date_from' => now()->subDays(5)->toDateString(),
            ]))
            ->assertOk()
            ->assertSee($recentTicket->ticket_number)
            ->assertDontSee($oldTicket->ticket_number);

        $this
            ->actingAs($reporter)
            ->get(route('tickets.index', [
                'date_to' => now()->subDays(5)->toDateString(),
            ]))
            ->assertOk()
            ->assertDontSee($recentTicket->ticket_number)
            ->assertSee($oldTicket->ticket_number);
    }

    // Pagination mempertahankan parameter filter pada halaman berikutnya.
    public function test_ticket_pagination_preserves_filter_query_string(): void
    {
        $reporter = User::factory()->create();

        Ticket::factory()
            ->count(11)
            ->for($reporter, 'reporter')
            ->create(['status' => TicketStatus::InProgress]);

        $response = $this
            ->actingAs($reporter)
            ->get(route('tickets.index', [
                'status' => TicketStatus::InProgress->value,
            ]));

        $tickets = $response->viewData('tickets');

        $response->assertOk();
        $this->assertStringContainsString(
            'status='.TicketStatus::InProgress->value,
            $tickets->url(2),
        );
    }

    // Jumlah query reporter tetap terbatas saat menampilkan sepuluh tiket.
    public function test_reporter_filter_list_avoids_n_plus_one_queries(): void
    {
        $reporter = User::factory()->create();
        Ticket::factory()
            ->count(15)
            ->for($reporter, 'reporter')
            ->create();

        DB::flushQueryLog();
        DB::enableQueryLog();

        $response = $this
            ->actingAs($reporter)
            ->get(route('tickets.index'));

        $queryCount = count(DB::getQueryLog());
        DB::disableQueryLog();

        $response->assertOk();
        $this->assertLessThanOrEqual(
            7,
            $queryCount,
            "Daftar reporter menjalankan {$queryCount} query.",
        );
    }
}
