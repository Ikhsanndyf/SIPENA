<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeveloperTicketController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TicketCommentController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Reporter melihat dan membuat tiket kendala.
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/create', [TicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');

    // Reporter melihat dan mengubah tiket miliknya yang masih new.
    Route::get('/tickets/{ticket}/edit', [TicketController::class, 'edit'])->name('tickets.edit');
    Route::put('/tickets/{ticket}', [TicketController::class, 'update'])->name('tickets.update');
    Route::delete('/tickets/{ticket}', [TicketController::class, 'destroy'])->name('tickets.destroy');
    Route::patch('/tickets/{ticket}/confirm', [TicketController::class, 'confirm'])->name('tickets.confirm');
    Route::post('/tickets/{ticket}/comments', [TicketCommentController::class, 'store'])->name('tickets.comments.store');
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');
});

// Area operasional hanya dapat diakses pengguna dengan role developer.
Route::prefix('developer')
    ->name('developer.')
    ->middleware(['auth', 'developer'])
    ->group(function (): void {
        Route::get('/dashboard', [DeveloperTicketController::class, 'dashboard'])
            ->name('dashboard');
        Route::get('/tickets', [DeveloperTicketController::class, 'index'])
            ->name('tickets.index');
        Route::get('/tickets/{ticket}', [DeveloperTicketController::class, 'show'])
            ->name('tickets.show');
        Route::patch(
            '/tickets/{ticket}/handling',
            [DeveloperTicketController::class, 'updateHandling'],
        )->name('tickets.handling');
        Route::patch(
            '/tickets/{ticket}/status',
            [DeveloperTicketController::class, 'updateStatus'],
        )->name('tickets.status');
        Route::post(
            '/tickets/{ticket}/comments',
            [TicketCommentController::class, 'store'],
        )->name('tickets.comments.store');
    });

require __DIR__.'/auth.php';
