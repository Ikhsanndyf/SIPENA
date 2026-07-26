<?php

namespace Tests\Feature;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Application;
use App\Models\Ticket;
use App\Models\TicketStatusHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DeveloperTicketAreaTest extends TestCase
{
    use RefreshDatabase;

    // Guest diarahkan ke login dari seluruh area developer.
    public function test_guest_is_redirected_to_login_from_developer_area(): void
    {
        $ticket = Ticket::factory()->create();

        $this->get(route('developer.dashboard'))
            ->assertRedirect(route('login'));
        $this->get(route('developer.tickets.index'))
            ->assertRedirect(route('login'));
        $this->get(route('developer.tickets.show', $ticket))
            ->assertRedirect(route('login'));
    }

    // Reporter ditolak middleware ketika membuka area developer.
    public function test_reporter_cannot_access_developer_area(): void
    {
        $reporter = User::factory()->create();
        $ticket = Ticket::factory()
            ->for($reporter, 'reporter')
            ->create();

        $this->actingAs($reporter)
            ->get(route('developer.dashboard'))
            ->assertForbidden();
        $this->actingAs($reporter)
            ->get(route('developer.tickets.index'))
            ->assertForbidden();
        $this->actingAs($reporter)
            ->get(route('developer.tickets.show', $ticket))
            ->assertForbidden();
    }

    // Dashboard umum mengarahkan developer ke dashboard operasional.
    public function test_developer_is_redirected_to_operational_dashboard(): void
    {
        $developer = User::factory()->developer()->create();

        $this
            ->actingAs($developer)
            ->get(route('dashboard'))
            ->assertRedirect(route('developer.dashboard'));
    }

    // Dashboard menampilkan agregasi, tiket terbaru, dan tiket mendesak.
    public function test_developer_dashboard_displays_accurate_summary(): void
    {
        $developer = User::factory()->developer()->create();

        Ticket::factory()->create([
            'status' => TicketStatus::New,
            'priority' => TicketPriority::Critical,
            'assigned_to' => null,
            'title' => 'Tiket kritis tanpa PIC',
        ]);
        Ticket::factory()->create([
            'status' => TicketStatus::WaitingConfirmation,
            'priority' => TicketPriority::High,
            'assigned_to' => $developer->id,
            'title' => 'Tiket menunggu konfirmasi',
        ]);

        $this
            ->actingAs($developer)
            ->get(route('developer.dashboard'))
            ->assertOk()
            ->assertViewIs('developer.dashboard')
            ->assertSee('Total Tiket')
            ->assertSee('Tiket kritis tanpa PIC')
            ->assertSee('Tiket menunggu konfirmasi')
            ->assertSee('Prioritas Kritis')
            ->assertSee('Belum Ada PIC');
    }

    // Developer melihat seluruh tiket dan dapat menerapkan kombinasi filter.
    public function test_developer_can_search_and_filter_all_tickets(): void
    {
        $developer = User::factory()->developer()->create();
        $application = Application::factory()->create();
        $matchingTicket = Ticket::factory()->create([
            'application_id' => $application->id,
            'title' => 'Gangguan sinkronisasi data khusus',
            'status' => TicketStatus::InProgress,
            'priority' => TicketPriority::High,
            'assigned_to' => $developer->id,
        ]);
        $otherTicket = Ticket::factory()->create([
            'title' => 'Masalah tampilan halaman',
            'status' => TicketStatus::New,
        ]);

        $this
            ->actingAs($developer)
            ->get(route('developer.tickets.index', [
                'search' => 'sinkronisasi',
                'status' => TicketStatus::InProgress->value,
                'priority' => TicketPriority::High->value,
                'application_id' => $application->id,
                'assigned_to' => $developer->id,
            ]))
            ->assertOk()
            ->assertSee($matchingTicket->ticket_number)
            ->assertDontSee($otherTicket->ticket_number);
    }

    // Jumlah query daftar tetap terbatas meskipun jumlah tiket bertambah.
    public function test_developer_ticket_list_avoids_n_plus_one_queries(): void
    {
        $developer = User::factory()->developer()->create();
        Ticket::factory()
            ->count(15)
            ->create(['assigned_to' => $developer->id]);

        DB::flushQueryLog();
        DB::enableQueryLog();

        $response = $this
            ->actingAs($developer)
            ->get(route('developer.tickets.index'));

        $queryCount = count(DB::getQueryLog());
        DB::disableQueryLog();

        $response->assertOk();
        $this->assertLessThanOrEqual(
            8,
            $queryCount,
            "Daftar developer menjalankan {$queryCount} query.",
        );
    }

    // Detail menampilkan data laporan, penanganan, dan timeline status.
    public function test_developer_can_view_complete_ticket_detail(): void
    {
        $developer = User::factory()->developer()->create();
        $ticket = Ticket::factory()->create([
            'status' => TicketStatus::Analyzed,
            'analysis_notes' => 'Masalah berasal dari validasi data.',
        ]);
        TicketStatusHistory::factory()
            ->for($ticket)
            ->for($developer, 'changedBy')
            ->create([
                'from_status' => TicketStatus::New,
                'to_status' => TicketStatus::Analyzed,
                'notes' => 'Analisis awal selesai.',
            ]);

        $this
            ->actingAs($developer)
            ->get(route('developer.tickets.show', $ticket))
            ->assertOk()
            ->assertViewIs('developer.tickets.show')
            ->assertSee($ticket->ticket_number)
            ->assertSee($ticket->reporter->name)
            ->assertSee('Masalah berasal dari validasi data.')
            ->assertSee('Analisis awal selesai.')
            ->assertSee('Data Penanganan')
            ->assertSee('Perubahan Status');
    }
}
