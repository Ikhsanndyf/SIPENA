@props(['priority'])

@php
    // Menormalisasi enum atau string prioritas untuk tampilan badge.
    $value = $priority instanceof \App\Enums\TicketPriority ? $priority->value : $priority;
    $label = match ($value) {
        'low' => 'Rendah',
        'medium' => 'Sedang',
        'high' => 'Tinggi',
        'critical' => 'Kritis',
        default => ucfirst($value),
    };
@endphp

<span {{ $attributes->class([
    'inline-flex rounded-full px-2.5 py-1 text-xs font-semibold',
    'bg-gray-100 text-gray-700' => $value === 'low',
    'bg-blue-100 text-blue-700' => $value === 'medium',
    'bg-orange-100 text-orange-700' => $value === 'high',
    'bg-red-100 text-red-700' => $value === 'critical',
]) }}>
    {{ $label }}
</span>
