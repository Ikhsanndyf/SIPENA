<?php

namespace App\Policies;

use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    // Semua pengguna terautentikasi dapat membuka daftar tiket.
    public function viewAny(User $user): bool
    {
        return true;
    }

    // Developer melihat semua tiket, sedangkan reporter hanya tiket miliknya.
    public function view(User $user, Ticket $ticket): bool
    {
        return $user->role === UserRole::Developer
            || $ticket->reporter_id === $user->id;
    }

    // Hanya reporter yang dapat membuat tiket.
    public function create(User $user): bool
    {
        return $user->role === UserRole::Reporter;
    }

    // Reporter hanya dapat mengubah tiket new miliknya.
    public function update(User $user, Ticket $ticket): bool
    {
        return $user->role === UserRole::Reporter
            && $ticket->reporter_id === $user->id
            && $ticket->status === TicketStatus::New;
    }

    // Aturan hapus sama dengan aturan perubahan tiket.
    public function delete(User $user, Ticket $ticket): bool
    {
        return $this->update($user, $ticket);
    }

    // Reporter pemilik mengonfirmasi tiket yang menunggu konfirmasi.
    public function confirm(User $user, Ticket $ticket): bool
    {
        return $user->role === UserRole::Reporter
            && $ticket->reporter_id === $user->id
            && $ticket->status === TicketStatus::WaitingConfirmation;
    }

    // Hanya developer yang dapat menangani tiket.
    public function handle(User $user, Ticket $ticket): bool
    {
        return $user->role === UserRole::Developer;
    }
}
