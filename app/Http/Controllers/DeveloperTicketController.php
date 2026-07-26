<?php

namespace App\Http\Controllers;

use App\Enums\TicketCategory;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Exceptions\InvalidTicketStatusTransition;
use App\Http\Requests\UpdateTicketHandlingRequest;
use App\Http\Requests\UpdateTicketStatusRequest;
use App\Models\Application;
use App\Models\Ticket;
use App\Models\User;
use App\Services\TicketStatusService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class DeveloperTicketController extends Controller
{
    public function dashboard(): View
    {
        // Menghitung seluruh kartu ringkasan dalam satu query agregasi.
        $summaryQuery = Ticket::query()
            ->selectRaw('COUNT(*) as total')
            ->selectRaw(
                'SUM(CASE WHEN priority = ? THEN 1 ELSE 0 END) as critical',
                [TicketPriority::Critical->value],
            )
            ->selectRaw(
                'SUM(CASE WHEN assigned_to IS NULL THEN 1 ELSE 0 END) as unassigned'
            );

        foreach (TicketStatus::cases() as $status) {
            $summaryQuery->selectRaw(
                "SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as status_{$status->value}",
                [$status->value],
            );
        }

        $summary = $summaryQuery->firstOrFail();

        // Mengambil lima tiket terbaru beserta relasi yang ditampilkan.
        $recentTickets = Ticket::query()
            ->with([
                'application:id,name',
                'reporter:id,name',
                'assignee:id,name',
            ])
            ->latest()
            ->limit(5)
            ->get();

        // Mengambil lima tiket mendesak dengan critical sebagai urutan pertama.
        $urgentTickets = Ticket::query()
            ->with([
                'application:id,name',
                'reporter:id,name',
                'assignee:id,name',
            ])
            ->whereIn('priority', [
                TicketPriority::High->value,
                TicketPriority::Critical->value,
            ])
            ->orderByRaw(
                'CASE WHEN priority = ? THEN 0 ELSE 1 END',
                [TicketPriority::Critical->value],
            )
            ->latest()
            ->limit(5)
            ->get();

        return view('developer.dashboard', compact(
            'summary',
            'recentTickets',
            'urgentTickets',
        ));
    }

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Ticket::class);

        // Menormalisasi filter agar hanya nilai domain yang diterapkan.
        $search = trim($request->string('search')->toString());
        $status = TicketStatus::tryFrom(
            $request->string('status')->toString()
        );
        $priority = TicketPriority::tryFrom(
            $request->string('priority')->toString()
        );
        $category = TicketCategory::tryFrom(
            $request->string('category')->toString()
        );

        // Memuat relasi tabel lebih awal untuk mencegah N+1 query.
        $tickets = Ticket::query()
            ->select([
                'id',
                'ticket_number',
                'reporter_id',
                'application_id',
                'assigned_to',
                'title',
                'category',
                'priority',
                'status',
                'created_at',
            ])
            ->with([
                'application:id,name',
                'reporter:id,name',
                'assignee:id,name',
            ])
            ->when($search !== '', function ($query) use ($search): void {
                // Mengelompokkan pencarian nomor, judul, dan reporter.
                $query->where(function ($query) use ($search): void {
                    $query
                        ->where('ticket_number', 'like', "%{$search}%")
                        ->orWhere('title', 'like', "%{$search}%")
                        ->orWhereHas('reporter', function ($query) use ($search): void {
                            $query->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->when($status, fn ($query) => $query->where('status', $status->value))
            ->when($priority, fn ($query) => $query->where('priority', $priority->value))
            ->when($category, fn ($query) => $query->where('category', $category->value))
            ->when(
                $request->integer('application_id') > 0,
                fn ($query) => $query->where(
                    'application_id',
                    $request->integer('application_id'),
                ),
            )
            ->when(
                $request->string('assigned_to')->toString() === 'unassigned',
                fn ($query) => $query->whereNull('assigned_to'),
            )
            ->when(
                $request->integer('assigned_to') > 0,
                fn ($query) => $query->where(
                    'assigned_to',
                    $request->integer('assigned_to'),
                ),
            )
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // Mengambil pilihan filter dengan kolom minimum yang diperlukan.
        $applications = Application::query()
            ->select(['id', 'name'])
            ->orderBy('name')
            ->get();
        $developers = $this->developers();

        return view('developer.tickets.index', [
            'tickets' => $tickets,
            'applications' => $applications,
            'developers' => $developers,
            'statuses' => TicketStatus::cases(),
            'priorities' => TicketPriority::cases(),
            'categories' => TicketCategory::cases(),
        ]);
    }

    public function show(
        Ticket $ticket,
        TicketStatusService $statusService,
    ): View {
        Gate::authorize('handle', $ticket);

        // Memuat seluruh relasi detail dalam jumlah query tetap.
        $ticket->load([
            'application:id,name',
            'reporter:id,name,email',
            'assignee:id,name',
            'attachment',
            'statusHistories.changedBy:id,name',
        ]);

        return view('developer.tickets.show', [
            'ticket' => $ticket,
            'developers' => $this->developers(),
            'priorities' => TicketPriority::cases(),
            'allowedTransitions' => $statusService
                ->allowedDeveloperTransitions($ticket->status),
        ]);
    }

    public function updateHandling(
        UpdateTicketHandlingRequest $request,
        Ticket $ticket,
    ): RedirectResponse {
        // Menyimpan seluruh field penanganan dalam satu operasi database.
        DB::transaction(function () use ($request, $ticket): void {
            $ticket->update($request->validated());
        });

        return redirect()
            ->route('developer.tickets.show', $ticket)
            ->with('success', 'Penanganan tiket berhasil diperbarui.');
    }

    public function updateStatus(
        UpdateTicketStatusRequest $request,
        Ticket $ticket,
        TicketStatusService $statusService,
    ): RedirectResponse {
        try {
            // Menyerahkan seluruh aturan workflow kepada service domain.
            $statusService->transitionByDeveloper(
                $ticket,
                $request->user(),
                TicketStatus::from($request->validated('status')),
                $request->validated('notes'),
            );
        } catch (InvalidTicketStatusTransition $exception) {
            // Mengembalikan pesan domain pada field status agar mudah dipahami.
            return back()
                ->withInput()
                ->withErrors(['status' => $exception->getMessage()]);
        }

        return redirect()
            ->route('developer.tickets.show', $ticket)
            ->with('success', 'Status tiket berhasil diperbarui.');
    }

    /**
     * Mengambil daftar developer untuk filter dan pilihan PIC.
     *
     * @return Collection<int, User>
     */
    private function developers()
    {
        return User::query()
            ->select(['id', 'name'])
            ->where('role', UserRole::Developer->value)
            ->orderBy('name')
            ->get();
    }
}
