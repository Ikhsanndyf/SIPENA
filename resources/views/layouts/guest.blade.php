<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SIPENA') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        {{-- Memuat hanya inti Font Awesome dan koleksi solid yang digunakan. --}}
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@7.2.0/css/fontawesome.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@7.2.0/css/solid.min.css">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-white font-sans text-slate-900 antialiased">
        <div class="min-h-[100dvh] md:grid md:h-[100dvh] md:grid-cols-[minmax(280px,38%)_1fr] md:overflow-hidden">
            {{-- Panel merek dibuat sederhana tanpa ornamen yang mengganggu form. --}}
            <aside class="hidden bg-indigo-700 px-10 py-9 text-white md:flex md:flex-col md:justify-between lg:px-14 lg:py-12">
                <a href="{{ url('/') }}" class="inline-flex w-fit items-center gap-3 rounded-md focus:outline-none">
                    {{-- Ikon tiket Font Awesome menjadi identitas halaman autentikasi. --}}
                    <span class="flex size-11 items-center justify-center rounded-xl bg-white shadow-sm">
                        <i class="fa-solid fa-ticket text-xl text-indigo-600" aria-hidden="true"></i>
                    </span>
                    <div>
                        <p class="font-bold leading-5">SIPENA</p>
                        <p class="text-xs text-indigo-200">Pelaporan Kendala Aplikasi</p>
                    </div>
                </a>

                <div class="max-w-md">
                    <p class="text-sm font-medium text-indigo-200">Satu kanal pelaporan</p>
                    <h1 class="mt-3 text-3xl font-bold leading-tight tracking-tight lg:text-4xl">
                        Laporkan kendala.<br>Pantau penyelesaiannya.
                    </h1>
                    <p class="mt-4 max-w-sm text-sm leading-6 text-indigo-100">
                        Informasi laporan, progres developer, dan riwayat penanganan tersimpan dalam satu sistem.
                    </p>
                </div>

                <p class="text-xs text-indigo-200">&copy; {{ now()->year }} SIPENA</p>
            </aside>

            {{-- Area form dibatasi agar tetap muat dalam satu viewport desktop. --}}
            <main class="flex min-h-[100dvh] items-center justify-center px-5 py-6 sm:px-8 md:min-h-0 md:py-5">
                <div class="w-full max-w-[420px]">
                    <a href="{{ url('/') }}" class="mb-6 inline-flex items-center gap-3 md:hidden">
                        <span class="flex size-9 items-center justify-center rounded-lg bg-indigo-600 text-white">
                            <i class="fa-solid fa-ticket text-base" aria-hidden="true"></i>
                        </span>
                        <span class="font-bold text-slate-900">SIPENA</span>
                    </a>

                    {{ $slot }}
                </div>
            </main>
        </div>
    </body>
</html>
