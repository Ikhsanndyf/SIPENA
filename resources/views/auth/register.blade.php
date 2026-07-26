<x-guest-layout>
    <div class="mb-5">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">Buat akun</h1>
        <p class="mt-1 text-sm text-slate-500">Akun baru akan terdaftar sebagai reporter.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-3.5">
        @csrf

        {{-- Identitas reporter. --}}
        <div>
            <x-input-label for="name" value="Nama Lengkap" />
            <input
                id="name"
                name="name"
                type="text"
                value="{{ old('name') }}"
                required
                autofocus
                autocomplete="name"
                placeholder="Nama lengkap"
                class="mt-1 block w-full rounded-lg border-slate-300 px-3.5 py-2.5 text-sm shadow-sm placeholder:text-slate-400 focus:border-indigo-500 focus:ring-indigo-500"
            >
            <x-input-error :messages="$errors->get('name')" class="mt-1" />
        </div>

        <div>
            <x-input-label for="email" value="Email" />
            <input
                id="email"
                name="email"
                type="email"
                value="{{ old('email') }}"
                required
                autocomplete="username"
                placeholder="nama@contoh.go.id"
                class="mt-1 block w-full rounded-lg border-slate-300 px-3.5 py-2.5 text-sm shadow-sm placeholder:text-slate-400 focus:border-indigo-500 focus:ring-indigo-500"
            >
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        {{-- Kedua input memakai kontrol visibilitas yang sama. --}}
        <div x-data="{ showPassword: false }" class="space-y-3.5">
            <div>
                <div class="flex items-center justify-between">
                    <x-input-label for="password" value="Kata Sandi" />
                    <button type="button" @click="showPassword = ! showPassword" class="text-xs font-medium text-indigo-600 hover:text-indigo-700">
                        <span x-text="showPassword ? 'Sembunyikan' : 'Tampilkan'">Tampilkan</span>
                    </button>
                </div>
                <input
                    id="password"
                    name="password"
                    :type="showPassword ? 'text' : 'password'"
                    required
                    autocomplete="new-password"
                    placeholder="Minimal 8 karakter"
                    class="mt-1 block w-full rounded-lg border-slate-300 px-3.5 py-2.5 text-sm shadow-sm placeholder:text-slate-400 focus:border-indigo-500 focus:ring-indigo-500"
                >
                <x-input-error :messages="$errors->get('password')" class="mt-1" />
            </div>

            <div>
                <x-input-label for="password_confirmation" value="Konfirmasi Kata Sandi" />
                <input
                    id="password_confirmation"
                    name="password_confirmation"
                    :type="showPassword ? 'text' : 'password'"
                    required
                    autocomplete="new-password"
                    placeholder="Ulangi kata sandi"
                    class="mt-1 block w-full rounded-lg border-slate-300 px-3.5 py-2.5 text-sm shadow-sm placeholder:text-slate-400 focus:border-indigo-500 focus:ring-indigo-500"
                >
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
            </div>
        </div>

        <button type="submit" class="w-full rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            Daftar
        </button>
    </form>

    <p class="mt-4 text-sm text-slate-500">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="font-semibold text-indigo-600 hover:text-indigo-700">Masuk</a>
    </p>
</x-guest-layout>
