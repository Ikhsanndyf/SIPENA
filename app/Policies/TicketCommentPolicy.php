<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;

class TicketCommentPolicy
{
    // Pengguna hanya dapat berkomentar jika memiliki akses melihat tiket.
    public function create(User $user, Ticket $ticket): bool
    {
        return $user->can('view', $ticket);
    }
}
