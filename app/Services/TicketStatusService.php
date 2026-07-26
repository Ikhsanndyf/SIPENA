<?php

namespace App\Services;

use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Exceptions\InvalidTicketStatusTransition;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;

class TicketStatusService
{
    /**
     * Peta seluruh transisi status yang diizinkan domain.
     *
     * @var array<string, list<string>>
     */
    private const TRANSITIONS = [
        'new' => ['analyzed', 'rejected'],
        'analyzed' => ['in_progress', 'rejected'],
        'in_progress' => ['waiting_confirmation', 'rejected'],
        'waiting_confirmation' => ['in_progress', 'resolved'],
        'resolved' => [],
        'rejected' => [],
    ];

    /**
     * Mengembalikan pilihan status yang boleh dilakukan developer.
     *
     * @return list<TicketStatus>
     */
    public function allowedDeveloperTransitions(TicketStatus $status): array
    {
        return array_values(array_filter(
            array_map(
                TicketStatus::from(...),
                self::TRANSITIONS[$status->value],
            ),
            fn (TicketStatus $target): bool => $target !== TicketStatus::Resolved,
        ));
    }

    /**
     * Menjalankan perubahan status oleh developer.
     */
    public function transitionByDeveloper(
        Ticket $ticket,
        User $actor,
        TicketStatus $target,
        ?string $notes = null,
    ): Ticket {
        if ($actor->role !== UserRole::Developer) {
            throw new AuthorizationException(
                'Hanya developer yang dapat mengubah status penanganan.'
            );
        }

        if ($target === TicketStatus::Resolved) {
            throw new InvalidTicketStatusTransition(
                'Status resolved hanya dapat dikonfirmasi reporter.'
            );
        }

        return $this->transition($ticket, $actor, $target, $notes);
    }

    /**
     * Menyelesaikan tiket melalui konfirmasi reporter pemilik.
     */
    public function confirmByReporter(Ticket $ticket, User $actor): Ticket
    {
        if ($actor->role !== UserRole::Reporter
            || $ticket->reporter_id !== $actor->id) {
            throw new AuthorizationException(
                'Hanya reporter pemilik yang dapat mengonfirmasi tiket.'
            );
        }

        return $this->transition(
            $ticket,
            $actor,
            TicketStatus::Resolved,
            'Reporter mengonfirmasi kendala telah selesai.',
        );
    }

    /**
     * Memvalidasi dan menyimpan transisi secara atomik.
     */
    private function transition(
        Ticket $ticket,
        User $actor,
        TicketStatus $target,
        ?string $notes,
    ): Ticket {
        DB::transaction(function () use ($ticket, $actor, $target, $notes): void {
            // Mengunci record agar dua perubahan status tidak diproses bersamaan.
            $lockedTicket = Ticket::query()
                ->lockForUpdate()
                ->findOrFail($ticket->id);
            $current = $lockedTicket->status;

            // Menolak target yang tidak tersedia pada transition map.
            if (! in_array(
                $target->value,
                self::TRANSITIONS[$current->value],
                true,
            )) {
                throw new InvalidTicketStatusTransition(
                    "Transisi dari {$current->value} ke {$target->value} tidak diizinkan."
                );
            }

            // Solusi wajib tersedia sebelum meminta konfirmasi reporter.
            if ($target === TicketStatus::WaitingConfirmation
                && blank($lockedTicket->resolution_notes)) {
                throw new InvalidTicketStatusTransition(
                    'Catatan solusi wajib diisi sebelum menunggu konfirmasi.'
                );
            }

            // Memperbarui status dan waktu penyelesaian tiket.
            $lockedTicket->update([
                'status' => $target,
                'resolved_at' => $target === TicketStatus::Resolved
                    ? now()
                    : null,
            ]);

            // Menyimpan jejak perubahan status yang tidak dapat diedit dari UI.
            $lockedTicket->statusHistories()->create([
                'changed_by' => $actor->id,
                'from_status' => $current,
                'to_status' => $target,
                'notes' => filled($notes) ? trim((string) $notes) : null,
            ]);
        });

        return $ticket->refresh();
    }
}
