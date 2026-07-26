<?php

namespace App\Http\Controllers;

use App\Enums\TicketCategory;
use App\Enums\TicketStatus;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Models\Application;
use App\Models\Ticket;
use App\Models\User;
use App\Services\TicketStatusService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use RuntimeException;
use Throwable;

class TicketController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Ticket::class);

        // Reporter hanya melihat tiket miliknya, diurutkan dari yang terbaru.
        $tickets = Ticket::query()
            ->with('application')
            ->where('reporter_id', $request->user()->id)
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('tickets.index', compact('tickets'));
    }

    public function create(): View
    {
        Gate::authorize('create', Ticket::class);

        // Menyiapkan pilihan aplikasi dan kategori untuk formulir reporter.
        $applications = Application::query()
            ->orderBy('name')
            ->get();

        return view('tickets.create', [
            'applications' => $applications,
            'categories' => TicketCategory::cases(),
        ]);
    }

    public function show(Ticket $ticket): View
    {
        Gate::authorize('view', $ticket);

        // Memuat informasi lengkap tiket untuk halaman detail.
        $ticket->load([
            'application',
            'reporter',
            'assignee',
            'attachment',
        ]);

        return view('tickets.show', compact('ticket'));
    }

    public function edit(Ticket $ticket): View
    {
        Gate::authorize('update', $ticket);

        // Menyiapkan data tiket dan pilihan field untuk formulir edit.
        $ticket->load('attachment');
        $applications = Application::query()
            ->orderBy('name')
            ->get();

        return view('tickets.edit', [
            'ticket' => $ticket,
            'applications' => $applications,
            'categories' => TicketCategory::cases(),
        ]);
    }

    public function store(StoreTicketRequest $request): RedirectResponse
    {
        $storedPath = null;

        try {
            $ticket = DB::transaction(function () use ($request, &$storedPath): Ticket {
                // Membuat tiket dengan nilai awal yang ditentukan sistem.
                $ticket = $request->user()->reportedTickets()->create(
                    $request->safe()->except('attachment')
                );

                // Membentuk nomor tiket setelah ID tiket tersedia.
                $ticket->update([
                    'ticket_number' => $this->generateTicketNumber($ticket),
                ]);

                // Mencatat status awal sebagai titik mulai workflow tiket.
                $ticket->statusHistories()->create([
                    'changed_by' => $request->user()->id,
                    'from_status' => null,
                    'to_status' => TicketStatus::New,
                    'notes' => 'Tiket dibuat oleh reporter.',
                ]);

                // Menyimpan file dan metadata lampiran opsional.
                if ($request->hasFile('attachment')) {
                    /** @var UploadedFile $file */
                    $file = $request->file('attachment');
                    $storedPath = $file->store('attachments', 'public');

                    if ($storedPath === false) {
                        throw new RuntimeException('Lampiran gagal disimpan.');
                    }

                    $ticket->attachment()->create([
                        'original_name' => $file->getClientOriginalName(),
                        'file_path' => $storedPath,
                        'mime_type' => $file->getMimeType() ?? $file->getClientMimeType(),
                        'file_size' => (int) $file->getSize(),
                    ]);
                }

                return $ticket;
            });
        } catch (Throwable $exception) {
            // Menghapus file jika transaksi database gagal.
            if (is_string($storedPath)) {
                Storage::disk('public')->delete($storedPath);
            }

            Log::error('Gagal membuat tiket.', [
                'user_id' => $request->user()?->id,
                'exception' => $exception,
            ]);

            throw $exception;
        }

        return redirect()
            ->route('dashboard')
            ->with('success', "Tiket {$ticket->ticket_number} berhasil dibuat.");
    }

    public function update(UpdateTicketRequest $request, Ticket $ticket): RedirectResponse
    {
        $storedPath = null;
        $oldPath = $ticket->attachment?->file_path;

        try {
            DB::transaction(function () use ($request, $ticket, &$storedPath): void {
                // Memperbarui field kendala yang boleh dikelola reporter.
                $ticket->update(
                    $request->safe()->except('attachment')
                );

                // Mengganti file dan metadata jika reporter mengunggah lampiran baru.
                if ($request->hasFile('attachment')) {
                    /** @var UploadedFile $file */
                    $file = $request->file('attachment');
                    $storedPath = $file->store('attachments', 'public');

                    if ($storedPath === false) {
                        throw new RuntimeException('Lampiran baru gagal disimpan.');
                    }

                    $ticket->attachment()->updateOrCreate([], [
                        'original_name' => $file->getClientOriginalName(),
                        'file_path' => $storedPath,
                        'mime_type' => $file->getMimeType() ?? $file->getClientMimeType(),
                        'file_size' => (int) $file->getSize(),
                    ]);
                }
            });
        } catch (Throwable $exception) {
            // Menghapus file baru ketika transaksi pembaruan gagal.
            if (is_string($storedPath)) {
                Storage::disk('public')->delete($storedPath);
            }

            Log::error('Gagal memperbarui tiket.', [
                'ticket_id' => $ticket->id,
                'user_id' => $request->user()?->id,
                'exception' => $exception,
            ]);

            throw $exception;
        }

        // Menghapus file lama setelah lampiran baru berhasil dicatat.
        if (is_string($storedPath) && is_string($oldPath) && $storedPath !== $oldPath) {
            Storage::disk('public')->delete($oldPath);
        }

        return redirect()
            ->route('tickets.show', $ticket)
            ->with('success', "Tiket {$ticket->ticket_number} berhasil diperbarui.");
    }

    public function destroy(Ticket $ticket): RedirectResponse
    {
        Gate::authorize('delete', $ticket);

        // Menyimpan informasi tiket dan lampiran sebelum record dihapus.
        $ticketNumber = $ticket->ticket_number;
        $attachmentPath = $ticket->attachment?->file_path;

        try {
            // Menghapus tiket beserta metadata lampiran melalui foreign key cascade.
            DB::transaction(function () use ($ticket): void {
                $ticket->delete();
            });
        } catch (Throwable $exception) {
            // Mencatat kegagalan database tanpa menghapus file fisik.
            Log::error('Gagal menghapus tiket.', [
                'ticket_id' => $ticket->id,
                'user_id' => request()->user()?->id,
                'exception' => $exception,
            ]);

            throw $exception;
        }

        // Menghapus file fisik setelah transaksi database berhasil.
        if (is_string($attachmentPath)
            && ! Storage::disk('public')->delete($attachmentPath)) {
            Log::warning('File lampiran tiket gagal dihapus.', [
                'ticket_number' => $ticketNumber,
                'file_path' => $attachmentPath,
            ]);
        }

        return redirect()
            ->route('tickets.index')
            ->with('success', "Tiket {$ticketNumber} berhasil dihapus.");
    }

    public function confirm(
        Request $request,
        Ticket $ticket,
        TicketStatusService $statusService,
    ): RedirectResponse {
        Gate::authorize('confirm', $ticket);

        // Reporter pemilik menyelesaikan workflow setelah memeriksa solusi.
        /** @var User $reporter */
        $reporter = $request->user();
        $statusService->confirmByReporter($ticket, $reporter);

        return redirect()
            ->route('tickets.show', $ticket)
            ->with('success', "Tiket {$ticket->ticket_number} berhasil dikonfirmasi selesai.");
    }

    private function generateTicketNumber(Ticket $ticket): string
    {
        return sprintf(
            'TCK-%s-%04d',
            $ticket->created_at->format('Ym'),
            $ticket->id,
        );
    }
}
