<?php

namespace App\Http\Controllers;

use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Menampilkan ringkasan tiket milik reporter.
     */
    public function index(Request $request): View|RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        // Developer menggunakan dashboard operasional yang lebih lengkap.
        if ($user->role === UserRole::Developer) {
            return redirect()->route('developer.dashboard');
        }

        // Seluruh angka ringkasan dihitung dalam satu query agregasi.
        $summary = Ticket::query()
            ->where('reporter_id', $user->id)
            ->selectRaw('COUNT(*) as total')
            ->selectRaw(
                'SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as status_new',
                [TicketStatus::New->value],
            )
            ->selectRaw(
                'SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as status_in_progress',
                [TicketStatus::InProgress->value],
            )
            ->selectRaw(
                'SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as status_waiting_confirmation',
                [TicketStatus::WaitingConfirmation->value],
            )
            ->selectRaw(
                'SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as status_resolved',
                [TicketStatus::Resolved->value],
            )
            ->firstOrFail();

        // Relasi yang tampil dimuat bersama untuk mencegah masalah N+1 query.
        $recentTickets = Ticket::query()
            ->where('reporter_id', $user->id)
            ->with([
                'application:id,name',
                'assignee:id,name',
            ])
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard', compact('summary', 'recentTickets'));
    }
}
