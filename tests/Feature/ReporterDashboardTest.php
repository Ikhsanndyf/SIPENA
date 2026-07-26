<?php

namespace Tests\Feature;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ReporterDashboardTest extends TestCase
{
    use RefreshDatabase;

    // Dashboard menghitung hanya tiket milik reporter yang sedang login.
    public function test_reporter_dashboard_displays_accurate_personal_summary(): void
    {
        $reporter = User::factory()->create(['name' => 'Reporter SIPENA']);
        $otherReporter = User::factory()->create();

        foreach ([
            TicketStatus::New,
            TicketStatus::InProgress,
            TicketStatus::WaitingConfirmation,
            TicketStatus::Resolved,
        ] as $status) {
            Ticket::factory()
                ->for($reporter, 'reporter')
                ->create(['status' => $status]);
        }

        Ticket::factory()
            ->for($otherReporter, 'reporter')
            ->create(['title' => 'Tiket milik reporter lain']);

        $response = $this
            ->actingAs($reporter)
            ->get(route('dashboard'));

        $response
            ->assertOk()
            ->assertViewIs('dashboard')
            ->assertSee('Selamat datang, Reporter SIPENA')
            ->assertSee('Ringkasan Tiket')
            ->assertDontSee('Tiket milik reporter lain');

        $summary = $response->viewData('summary');

        $this->assertSame(4, (int) $summary->total);
        $this->assertSame(1, (int) $summary->status_new);
        $this->assertSame(1, (int) $summary->status_in_progress);
        $this->assertSame(1, (int) $summary->status_waiting_confirmation);
        $this->assertSame(1, (int) $summary->status_resolved);
    }

    // Daftar ringkas membatasi lima tiket dan mengurutkan yang terbaru.
    public function test_dashboard_displays_only_five_latest_personal_tickets(): void
    {
        $reporter = User::factory()->create();

        $tickets = Ticket::factory()
            ->count(6)
            ->for($reporter, 'reporter')
            ->sequence(fn ($sequence) => [
                'title' => 'Laporan urutan '.($sequence->index + 1),
                'created_at' => now()->addMinutes($sequence->index),
                'updated_at' => now()->addMinutes($sequence->index),
            ])
            ->create();

        $response = $this
            ->actingAs($reporter)
            ->get(route('dashboard'));

        $response
            ->assertOk()
            ->assertSeeInOrder([
                'Laporan urutan 6',
                'Laporan urutan 5',
                'Laporan urutan 4',
                'Laporan urutan 3',
                'Laporan urutan 2',
            ])
            ->assertDontSee('Laporan urutan 1');

        $this->assertCount(5, $response->viewData('recentTickets'));
        $this->assertNotNull($tickets->last());
    }

    // Kondisi kosong memberi arahan yang dapat langsung ditindaklanjuti.
    public function test_empty_dashboard_guides_reporter_to_create_first_ticket(): void
    {
        $reporter = User::factory()->create();

        $this
            ->actingAs($reporter)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Belum ada tiket')
            ->assertSee('Buat Tiket Pertama')
            ->assertSee(route('tickets.create'));
    }

    // Eager loading menjaga jumlah query tetap stabil saat tiket bertambah.
    public function test_reporter_dashboard_avoids_n_plus_one_queries(): void
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
            ->get(route('dashboard'));

        $queryCount = count(DB::getQueryLog());
        DB::disableQueryLog();

        $response->assertOk();
        $this->assertLessThanOrEqual(
            5,
            $queryCount,
            "Dashboard reporter menjalankan {$queryCount} query.",
        );
    }

    // Navigasi reporter tidak menampilkan menu operasional developer.
    public function test_navigation_is_adapted_to_reporter_role(): void
    {
        $reporter = User::factory()->create();

        $this
            ->actingAs($reporter)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Tiket Saya')
            ->assertSee('Buat Tiket')
            ->assertDontSee('Kelola Tiket');
    }
}
