<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketRequest;
use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class TicketController extends Controller
{
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

    private function generateTicketNumber(Ticket $ticket): string
    {
        return sprintf(
            'TCK-%s-%04d',
            $ticket->created_at->format('Ym'),
            $ticket->id,
        );
    }
}
