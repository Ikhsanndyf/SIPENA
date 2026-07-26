@props(['status'])

@php
    // Menormalisasi enum atau string agar komponen dapat digunakan ulang.
    $value = $status instanceof \App\Enums\TicketStatus ? $status->value : $status;
    $label = match ($value) {
        'new' => 'Baru',
        'analyzed' => 'Dianalisis',
        'in_progress' => 'Dikerjakan',
        'waiting_confirmation' => 'Menunggu Konfirmasi',
        'resolved' => 'Selesai',
        'rejected' => 'Ditolak',
        default => ucfirst(str_replace('_', ' ', $value)),
    };
@endphp

<span {{ $attributes->class([
    'inline-flex rounded-full px-2.5 py-1 text-xs font-semibold',
    'bg-gray-100 text-gray-700' => $value === 'new',
    'bg-blue-100 text-blue-700' => $value === 'analyzed',
    'bg-amber-100 text-amber-700' => $value === 'in_progress',
    'bg-purple-100 text-purple-700' => $value === 'waiting_confirmation',
    'bg-green-100 text-green-700' => $value === 'resolved',
    'bg-red-100 text-red-700' => $value === 'rejected',
]) }}>
    {{ $label }}
</span>
