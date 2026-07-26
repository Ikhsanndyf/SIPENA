<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-medium text-indigo-600">Beranda Reporter</p>
                <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">
                    Selamat datang, {{ auth()->user()->name }}
                </h1>
                <p class="mt-1 text-sm text-slate-500">
                    Pantau laporan kendala aplikasi Anda dalam satu tempat.
                </p>
            </div>

            <a
                href="{{ route('tickets.create') }}"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
            >
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Laporkan Kendala
            </a>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="mx-auto max-w-7xl space-y-8 px-4 sm:px-6 lg:px-8">
            {{-- Ringkasan tiket reporter dari satu query agregasi. --}}
            <section aria-labelledby="ringkasan-tiket">
                <div class="mb-4 flex items-end justify-between">
                    <div>
                        <h2 id="ringkasan-tiket" class="text-lg font-semibold text-slate-900">Ringkasan Tiket</h2>
                        <p class="mt-1 text-sm text-slate-500">Kondisi terbaru seluruh laporan Anda.</p>
                    </div>
                    <a href="{{ route('tickets.index') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-700">
                        Lihat semua
                    </a>
                </div>

                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
                    @php
                        $cards = [
                            ['label' => 'Total Tiket', 'value' => $summary->total, 'description' => 'Seluruh laporan Anda', 'classes' => 'border-slate-200 bg-white text-slate-900', 'icon' => 'text-slate-500 bg-slate-100'],
                            ['label' => 'Baru', 'value' => $summary->status_new, 'description' => 'Menunggu ditinjau', 'classes' => 'border-sky-200 bg-sky-50 text-sky-950', 'icon' => 'text-sky-700 bg-sky-100'],
                            ['label' => 'Dikerjakan', 'value' => $summary->status_in_progress, 'description' => 'Sedang ditangani', 'classes' => 'border-amber-200 bg-amber-50 text-amber-950', 'icon' => 'text-amber-700 bg-amber-100'],
                            ['label' => 'Perlu Konfirmasi', 'value' => $summary->status_waiting_confirmation, 'description' => 'Periksa solusi developer', 'classes' => 'border-violet-200 bg-violet-50 text-violet-950', 'icon' => 'text-violet-700 bg-violet-100'],
                            ['label' => 'Selesai', 'value' => $summary->status_resolved, 'description' => 'Kendala terselesaikan', 'classes' => 'border-emerald-200 bg-emerald-50 text-emerald-950', 'icon' => 'text-emerald-700 bg-emerald-100'],
                        ];
                    @endphp

                    @foreach ($cards as $card)
                        <article class="rounded-2xl border p-5 shadow-sm {{ $card['classes'] }}">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold">{{ $card['label'] }}</p>
                                    <p class="mt-3 text-3xl font-bold">{{ (int) $card['value'] }}</p>
                                </div>
                                <span class="rounded-xl p-2.5 {{ $card['icon'] }}">
                                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                </span>
                            </div>
                            <p class="mt-2 text-xs opacity-70">{{ $card['description'] }}</p>
                        </article>
                    @endforeach
                </div>
            </section>

            <div class="grid gap-6 xl:grid-cols-[minmax(0,2fr)_minmax(280px,1fr)]">
                {{-- Lima tiket terbaru beserta status penanganannya. --}}
                <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm" aria-labelledby="tiket-terbaru">
                    <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4 sm:px-6">
                        <div>
                            <h2 id="tiket-terbaru" class="font-semibold text-slate-900">Tiket Terbaru</h2>
                            <p class="mt-1 text-sm text-slate-500">Lima laporan terakhir yang Anda buat.</p>
                        </div>
                        <a href="{{ route('tickets.index') }}" class="hidden text-sm font-semibold text-indigo-600 hover:text-indigo-700 sm:block">
                            Kelola tiket
                        </a>
                    </div>

                    @if ($recentTickets->isEmpty())
                        <div class="px-6 py-14 text-center">
                            <span class="mx-auto flex size-12 items-center justify-center rounded-full bg-indigo-50 text-indigo-600">
                                <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5A3.375 3.375 0 0 0 10.125 2.25H8.25m0 12.75h7.5m-7.5 3h4.5m-1.5-15.75H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V10.5a8.25 8.25 0 0 0-8.25-8.25Z" />
                                </svg>
                            </span>
                            <h3 class="mt-4 font-semibold text-slate-900">Belum ada tiket</h3>
                            <p class="mx-auto mt-2 max-w-sm text-sm text-slate-500">
                                Buat tiket saat menemukan kendala agar tim developer dapat segera menanganinya.
                            </p>
                            <a href="{{ route('tickets.create') }}" class="mt-5 inline-flex rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                                Buat Tiket Pertama
                            </a>
                        </div>
                    @else
                        <div class="divide-y divide-slate-100">
                            @foreach ($recentTickets as $ticket)
                                <a
                                    href="{{ route('tickets.show', $ticket) }}"
                                    class="group block px-5 py-4 transition hover:bg-slate-50 sm:px-6"
                                >
                                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                        <div class="min-w-0">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <span class="font-mono text-xs font-semibold text-indigo-600">{{ $ticket->ticket_number }}</span>
                                                <x-ticket-status-badge :status="$ticket->status" />
                                            </div>
                                            <p class="mt-2 truncate font-semibold text-slate-900 group-hover:text-indigo-700">
                                                {{ $ticket->title }}
                                            </p>
                                            <p class="mt-1 text-sm text-slate-500">
                                                {{ $ticket->application->name }}
                                                <span aria-hidden="true">·</span>
                                                {{ $ticket->created_at->translatedFormat('d M Y, H:i') }}
                                            </p>
                                        </div>
                                        <svg class="hidden size-5 shrink-0 text-slate-400 transition group-hover:translate-x-0.5 group-hover:text-indigo-600 sm:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m9 18 6-6-6-6" />
                                        </svg>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </section>

                {{-- Panduan singkat agar alur tindak lanjut mudah dipahami. --}}
                <aside class="rounded-2xl bg-slate-900 p-6 text-white shadow-sm">
                    <span class="inline-flex rounded-full bg-white/10 px-3 py-1 text-xs font-semibold text-indigo-200">
                        Alur SIPENA
                    </span>
                    <h2 class="mt-4 text-lg font-semibold">Apa yang perlu dilakukan?</h2>
                    <ol class="mt-5 space-y-5">
                        <li class="flex gap-3">
                            <span class="flex size-7 shrink-0 items-center justify-center rounded-full bg-indigo-500 text-xs font-bold">1</span>
                            <div>
                                <p class="text-sm font-semibold">Laporkan kendala</p>
                                <p class="mt-1 text-xs leading-5 text-slate-300">Isi informasi dan langkah reproduksi secara jelas.</p>
                            </div>
                        </li>
                        <li class="flex gap-3">
                            <span class="flex size-7 shrink-0 items-center justify-center rounded-full bg-indigo-500 text-xs font-bold">2</span>
                            <div>
                                <p class="text-sm font-semibold">Pantau penanganan</p>
                                <p class="mt-1 text-xs leading-5 text-slate-300">Developer menganalisis, mengerjakan, dan memberi solusi.</p>
                            </div>
                        </li>
                        <li class="flex gap-3">
                            <span class="flex size-7 shrink-0 items-center justify-center rounded-full bg-indigo-500 text-xs font-bold">3</span>
                            <div>
                                <p class="text-sm font-semibold">Konfirmasi solusi</p>
                                <p class="mt-1 text-xs leading-5 text-slate-300">Uji solusi lalu konfirmasi jika kendala sudah selesai.</p>
                            </div>
                        </li>
                    </ol>
                </aside>
            </div>
        </div>
    </div>
</x-app-layout>
