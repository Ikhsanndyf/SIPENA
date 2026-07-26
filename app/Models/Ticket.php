<?php

namespace App\Models;

use App\Enums\TicketCategory;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use Database\Factories\TicketFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Ticket extends Model
{
    /** @use HasFactory<TicketFactory> */
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'reporter_id',
        'application_id',
        'assigned_to',
        'title',
        'category',
        'priority',
        'status',
        'description',
        'reproduction_steps',
        'analysis_notes',
        'resolution_notes',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'category' => TicketCategory::class,
            'priority' => TicketPriority::class,
            'status' => TicketStatus::class,
            'resolved_at' => 'datetime',
        ];
    }

    // Menggunakan nomor tiket sebagai identitas pada URL.
    public function getRouteKeyName(): string
    {
        return 'ticket_number';
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function attachment(): HasOne
    {
        return $this->hasOne(Attachment::class);
    }

    // Menampilkan riwayat status terbaru terlebih dahulu.
    public function statusHistories(): HasMany
    {
        return $this->hasMany(TicketStatusHistory::class)
            ->latest();
    }

    // Menghubungkan tiket dengan komentar tanpa urutan bawaan.
    public function comments(): HasMany
    {
        return $this->hasMany(TicketComment::class);
    }

    /**
     * Menerapkan filter tiket yang dipakai reporter dan developer.
     *
     * @param  Builder<Ticket>  $query
     * @param  array<string, mixed>  $filters
     * @return Builder<Ticket>
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        $search = trim((string) ($filters['search'] ?? ''));
        $assignedTo = $filters['assigned_to'] ?? null;

        return $query
            ->when($search !== '', function (Builder $query) use ($search): void {
                // Pencarian dikelompokkan agar tidak mengabaikan filter lain.
                $query->where(function (Builder $query) use ($search): void {
                    $query
                        ->where('ticket_number', 'like', "%{$search}%")
                        ->orWhere('title', 'like', "%{$search}%")
                        ->orWhereHas('reporter', function (Builder $query) use ($search): void {
                            $query->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->when(
                filled($filters['application_id'] ?? null),
                fn (Builder $query) => $query->where(
                    'application_id',
                    $filters['application_id'],
                ),
            )
            ->when(
                filled($filters['status'] ?? null),
                fn (Builder $query) => $query->where(
                    'status',
                    $filters['status'],
                ),
            )
            ->when(
                filled($filters['priority'] ?? null),
                fn (Builder $query) => $query->where(
                    'priority',
                    $filters['priority'],
                ),
            )
            ->when(
                filled($filters['category'] ?? null),
                fn (Builder $query) => $query->where(
                    'category',
                    $filters['category'],
                ),
            )
            ->when(
                $assignedTo === 'unassigned',
                fn (Builder $query) => $query->whereNull('assigned_to'),
            )
            ->when(
                filled($assignedTo) && $assignedTo !== 'unassigned',
                fn (Builder $query) => $query->where(
                    'assigned_to',
                    $assignedTo,
                ),
            )
            ->when(
                filled($filters['date_from'] ?? null),
                fn (Builder $query) => $query->whereDate(
                    'created_at',
                    '>=',
                    $filters['date_from'],
                ),
            )
            ->when(
                filled($filters['date_to'] ?? null),
                fn (Builder $query) => $query->whereDate(
                    'created_at',
                    '<=',
                    $filters['date_to'],
                ),
            );
    }
}
