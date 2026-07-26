<?php

namespace App\Models;

use App\Enums\TicketStatus;
use Database\Factories\TicketStatusHistoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketStatusHistory extends Model
{
    /** @use HasFactory<TicketStatusHistoryFactory> */
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'changed_by',
        'from_status',
        'to_status',
        'notes',
    ];

    /**
     * Mengubah nilai status menjadi enum domain.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'from_status' => TicketStatus::class,
            'to_status' => TicketStatus::class,
        ];
    }

    // Menghubungkan riwayat dengan tiket yang berubah.
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    // Menghubungkan riwayat dengan pengguna yang melakukan perubahan.
    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
