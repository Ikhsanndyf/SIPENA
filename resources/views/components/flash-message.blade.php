@php
    // Menentukan satu pesan flash utama agar tampil konsisten di seluruh halaman.
    $flash = collect([
        'success' => session('success'),
        'error' => session('error'),
        'warning' => session('warning'),
        'status' => session('status'),
    ])->first(fn ($message) => filled($message));

    $type = collect(['success', 'error', 'warning', 'status'])
        ->first(fn ($key) => filled(session($key)));

    $styles = match ($type) {
        'error' => 'border-red-200 bg-red-50 text-red-800',
        'warning' => 'border-amber-200 bg-amber-50 text-amber-800',
        default => 'border-emerald-200 bg-emerald-50 text-emerald-800',
    };
@endphp

@if ($flash)
    <div
        x-data="{ visible: true }"
        x-show="visible"
        x-transition
        role="alert"
        data-flash-message
        data-flash-type="{{ $type }}"
        class="mx-auto mt-5 max-w-7xl px-4 sm:px-6 lg:px-8"
    >
        <div class="flex items-start justify-between gap-4 rounded-xl border px-4 py-3 text-sm font-medium shadow-sm {{ $styles }}">
            <p>{{ $flash }}</p>
            <button type="button" @click="visible = false" class="shrink-0 rounded p-0.5 opacity-60 hover:opacity-100" aria-label="Tutup notifikasi">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>
@endif
