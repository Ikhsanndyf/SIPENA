<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketCommentRequest;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

class TicketCommentController extends Controller
{
    public function store(
        StoreTicketCommentRequest $request,
        Ticket $ticket,
    ): RedirectResponse {
        // Menyimpan komentar melalui relasi agar ticket_id tidak dapat dimanipulasi.
        /** @var User $user */
        $user = $request->user();
        $ticket->comments()->create([
            'user_id' => $user->id,
            'body' => $request->validated('body'),
        ]);

        return back()->with('success', 'Komentar berhasil ditambahkan.');
    }
}
