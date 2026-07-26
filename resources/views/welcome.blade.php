<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="SIPENA membantu pelaporan dan pemantauan kendala aplikasi.">

        <title>SIPENA — Pelaporan Kendala Aplikasi</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-slate-50 font-sans text-slate-900 antialiased">
        <div class="flex min-h-[100dvh] flex-col">
            {{-- Navigasi publik dengan aksi yang menyesuaikan status autentikasi. --}}
            <header class="border-b border-slate-200 bg-white">
                <nav class="mx-auto flex h-16 max-w-7xl items-center justify-between px-5 sm:px-8">
                    <a href="{{ url('/') }}" class="flex items-center gap-3 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <x-application-logo class="size-9 text-indigo-600" />
                        <div>
                            <p class="text-sm font-bold leading-4 tracking-tight">SIPENA</p>
                            <p class="hidden text-xs text-slate-500 sm:block">Pelaporan Kendala Aplikasi</p>
                        </div>
                    </a>

                    <div class="flex items-center gap-2">
                        @auth
                            <a href="{{ route('dashboard') }}" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                Buka Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="rounded-lg px-3 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 hover:text-slate-900">
                                Masuk
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                    Daftar
                                </a>
                            @endif
                        @endauth
                    </div>
                </nav>
            </header>

            {{-- Hero menjelaskan tujuan SIPENA dan menampilkan gambaran produk. --}}
            <main class="relative flex flex-1 items-center overflow-hidden">
                <div class="pointer-events-none absolute inset-0 bg-[linear-gradient(to_right,#e2e8f0_1px,transparent_1px),linear-gradient(to_bottom,#e2e8f0_1px,transparent_1px)] bg-[size:40px_40px] opacity-30 [mask-image:linear-gradient(to_bottom,black,transparent_85%)]"></div>

                <div class="relative mx-auto grid w-full max-w-7xl items-center gap-12 px-5 py-12 sm:px-8 lg:grid-cols-[0.9fr_1.1fr] lg:py-14">
                    <section class="max-w-xl">
                        <div class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-600 shadow-sm">
                            <span class="size-2 rounded-full bg-emerald-500"></span>
                            Sistem pelaporan internal
                        </div>

                        <h1 class="mt-5 text-4xl font-bold leading-[1.12] tracking-tight text-slate-950 sm:text-5xl">
                            Kendala aplikasi,<br>
                            <span class="text-indigo-600">ditangani lebih jelas.</span>
                        </h1>
                        <p class="mt-5 max-w-lg text-base leading-7 text-slate-600">
                            SIPENA menghubungkan reporter dan developer dalam satu alur pelaporan, penanganan, dan konfirmasi solusi yang terdokumentasi.
                        </p>

                        <div class="mt-7 flex flex-wrap gap-3">
                            @auth
                                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700">
                                    Buka Dashboard
                                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 18 6-6-6-6" />
                                    </svg>
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700">
                                    Masuk ke SIPENA
                                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 18 6-6-6-6" />
                                    </svg>
                                </a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-400 hover:bg-slate-50">
                                        Buat Akun Reporter
                                    </a>
                                @endif
                            @endauth
                        </div>

                        {{-- Ringkasan kemampuan utama tanpa menambah blok konten berlebihan. --}}
                        <dl class="mt-9 grid grid-cols-3 divide-x divide-slate-200 border-t border-slate-200 pt-5">
                            <div class="pr-4">
                                <dt class="text-sm font-semibold text-slate-900">Terpusat</dt>
                                <dd class="mt-1 text-xs leading-5 text-slate-500">Satu kanal laporan</dd>
                            </div>
                            <div class="px-4">
                                <dt class="text-sm font-semibold text-slate-900">Terpantau</dt>
                                <dd class="mt-1 text-xs leading-5 text-slate-500">Status transparan</dd>
                            </div>
                            <div class="pl-4">
                                <dt class="text-sm font-semibold text-slate-900">Tercatat</dt>
                                <dd class="mt-1 text-xs leading-5 text-slate-500">Riwayat lengkap</dd>
                            </div>
                        </dl>
                    </section>

                    {{-- Mockup dashboard memberi konteks produk tanpa gambar eksternal. --}}
                    <section class="relative mx-auto w-full max-w-2xl" aria-label="Pratinjau antarmuka SIPENA">
                        <div class="absolute -inset-4 rounded-3xl bg-indigo-100/70 blur-2xl"></div>
                        <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl shadow-slate-300/50">
                            <div class="flex h-12 items-center justify-between border-b border-slate-200 px-4">
                                <div class="flex items-center gap-1.5" aria-hidden="true">
                                    <span class="size-2.5 rounded-full bg-slate-300"></span>
                                    <span class="size-2.5 rounded-full bg-slate-300"></span>
                                    <span class="size-2.5 rounded-full bg-slate-300"></span>
                                </div>
                                <span class="text-xs font-medium text-slate-400">Dashboard Reporter</span>
                                <span class="size-7 rounded-full bg-indigo-100"></span>
                            </div>

                            <div class="grid sm:grid-cols-[9rem_1fr]">
                                <div class="hidden border-r border-slate-200 bg-slate-50 p-4 sm:block">
                                    <div class="flex items-center gap-2">
                                        <x-application-logo class="size-7 text-indigo-600" />
                                        <span class="text-xs font-bold text-slate-700">SIPENA</span>
                                    </div>
                                    <div class="mt-6 space-y-2">
                                        <div class="rounded-md bg-indigo-100 px-3 py-2 text-xs font-semibold text-indigo-700">Dashboard</div>
                                        <div class="px-3 py-2 text-xs font-medium text-slate-500">Tiket Saya</div>
                                        <div class="px-3 py-2 text-xs font-medium text-slate-500">Buat Tiket</div>
                                    </div>
                                </div>

                                <div class="min-w-0 p-4 sm:p-5">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <p class="text-sm font-bold text-slate-900">Ringkasan Tiket</p>
                                            <p class="mt-0.5 text-[11px] text-slate-400">Kondisi laporan Anda saat ini</p>
                                        </div>
                                        <span class="rounded-md bg-indigo-600 px-2.5 py-1.5 text-[10px] font-semibold text-white">+ Buat Tiket</span>
                                    </div>

                                    <div class="mt-4 grid grid-cols-3 gap-2">
                                        <div class="rounded-lg border border-slate-200 p-3">
                                            <p class="text-[10px] font-medium text-slate-500">Total</p>
                                            <p class="mt-1 text-xl font-bold text-slate-900">12</p>
                                        </div>
                                        <div class="rounded-lg border border-amber-200 bg-amber-50 p-3">
                                            <p class="text-[10px] font-medium text-amber-700">Dikerjakan</p>
                                            <p class="mt-1 text-xl font-bold text-amber-900">3</p>
                                        </div>
                                        <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-3">
                                            <p class="text-[10px] font-medium text-emerald-700">Selesai</p>
                                            <p class="mt-1 text-xl font-bold text-emerald-900">8</p>
                                        </div>
                                    </div>

                                    <div class="mt-4 overflow-hidden rounded-lg border border-slate-200">
                                        <div class="border-b border-slate-200 bg-slate-50 px-3 py-2">
                                            <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-500">Tiket terbaru</p>
                                        </div>
                                        @foreach ([
                                            ['TCK-202607-0012', 'Gagal mengunggah dokumen', 'Dikerjakan', 'bg-amber-100 text-amber-700'],
                                            ['TCK-202607-0011', 'Data laporan tidak tampil', 'Dianalisis', 'bg-sky-100 text-sky-700'],
                                            ['TCK-202607-0010', 'Akun tidak dapat masuk', 'Selesai', 'bg-emerald-100 text-emerald-700'],
                                        ] as [$number, $title, $status, $classes])
                                            <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-3 py-2.5 last:border-0">
                                                <div class="min-w-0">
                                                    <p class="font-mono text-[9px] font-semibold text-indigo-600">{{ $number }}</p>
                                                    <p class="mt-0.5 truncate text-[11px] font-medium text-slate-700">{{ $title }}</p>
                                                </div>
                                                <span class="shrink-0 rounded-full px-2 py-1 text-[9px] font-semibold {{ $classes }}">{{ $status }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </main>

            <footer class="border-t border-slate-200 bg-white py-3">
                <div class="mx-auto flex max-w-7xl items-center justify-between px-5 text-xs text-slate-400 sm:px-8">
                    <p>© {{ now()->year }} SIPENA</p>
                    <p class="hidden sm:block">Sistem Pelaporan dan Penanganan Kendala Aplikasi</p>
                </div>
            </footer>
        </div>
    </body>
</html>
