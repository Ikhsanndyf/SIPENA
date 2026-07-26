<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title') — SIPENA</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <main class="flex min-h-screen items-center justify-center bg-slate-50 px-4 py-12">
            <section class="w-full max-w-lg text-center">
                {{-- Identitas aplikasi menjaga halaman error tetap mudah dikenali. --}}
                <a href="{{ url('/') }}" class="inline-flex items-center gap-3">
                    <x-application-logo class="size-11 text-indigo-600" />
                    <span class="text-xl font-bold text-slate-900">SIPENA</span>
                </a>

                <div class="mt-8 rounded-2xl border border-slate-200 bg-white p-8 shadow-sm sm:p-10">
                    <p class="text-sm font-bold tracking-widest text-indigo-600">@yield('code')</p>
                    <h1 class="mt-3 text-2xl font-bold text-slate-900">@yield('heading')</h1>
                    <p class="mt-3 text-sm leading-6 text-slate-500">@yield('message')</p>

                    {{-- Tombol pemulihan mengarahkan pengguna ke halaman yang aman. --}}
                    <div class="mt-7 flex flex-col justify-center gap-3 sm:flex-row">
                        <button
                            type="button"
                            onclick="history.back()"
                            class="rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                        >
                            Kembali
                        </button>
                        <a
                            href="{{ auth()->check() ? route('dashboard') : url('/') }}"
                            class="rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700"
                        >
                            {{ auth()->check() ? 'Ke Dashboard' : 'Ke Halaman Utama' }}
                        </a>
                    </div>
                </div>
            </section>
        </main>
    </body>
</html>
