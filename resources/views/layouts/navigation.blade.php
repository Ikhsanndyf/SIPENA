<nav x-data="{ open: false }" class="sticky top-0 z-40 border-b border-slate-200 bg-white/95 backdrop-blur">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-18 items-center justify-between py-3">
            <div class="flex min-w-0 items-center gap-8">
                {{-- Identitas aplikasi mengarah ke dashboard sesuai role. --}}
                <a href="{{ route('dashboard') }}" class="flex shrink-0 items-center gap-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <x-application-logo class="size-10 text-indigo-600" />
                    <span>
                        <span class="block text-base font-bold leading-5 tracking-tight text-slate-900">SIPENA</span>
                        <span class="hidden text-xs text-slate-500 lg:block">Pelaporan Kendala Aplikasi</span>
                    </span>
                </a>

                {{-- Menu desktop disesuaikan dengan tanggung jawab pengguna. --}}
                <div class="hidden items-center gap-1 sm:flex">
                    @if (auth()->user()->role === \App\Enums\UserRole::Developer)
                        <x-nav-link :href="route('developer.dashboard')" :active="request()->routeIs('developer.dashboard')">
                            Dashboard
                        </x-nav-link>
                        <x-nav-link :href="route('developer.tickets.index')" :active="request()->routeIs('developer.tickets.*')">
                            Kelola Tiket
                        </x-nav-link>
                    @else
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                            Dashboard
                        </x-nav-link>
                        <x-nav-link :href="route('tickets.index')" :active="request()->routeIs('tickets.index', 'tickets.show', 'tickets.edit')">
                            Tiket Saya
                        </x-nav-link>
                        <x-nav-link :href="route('tickets.create')" :active="request()->routeIs('tickets.create')">
                            Buat Tiket
                        </x-nav-link>
                    @endif
                </div>
            </div>

            {{-- Informasi akun dan akses pengaturan. --}}
            <div class="hidden items-center gap-3 sm:flex">
                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                    {{ auth()->user()->role === \App\Enums\UserRole::Developer ? 'Developer' : 'Reporter' }}
                </span>

                <x-dropdown align="right" width="48" contentClasses="py-1 bg-white">
                    <x-slot name="trigger">
                        <button class="flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-2.5 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <span class="flex size-8 items-center justify-center rounded-lg bg-indigo-100 text-sm font-bold uppercase text-indigo-700">
                                {{ mb_substr(auth()->user()->name, 0, 1) }}
                            </span>
                            <span class="hidden max-w-32 truncate lg:block">{{ auth()->user()->name }}</span>
                            <svg class="size-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m19.5 9-7.5 7.5L4.5 9" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="border-b border-slate-100 px-4 py-3">
                            <p class="truncate text-sm font-semibold text-slate-900">{{ auth()->user()->name }}</p>
                            <p class="mt-0.5 truncate text-xs text-slate-500">{{ auth()->user()->email }}</p>
                        </div>
                        <x-dropdown-link :href="route('profile.edit')">Pengaturan Profil</x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link
                                :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();"
                            >
                                Keluar
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            {{-- Tombol menu untuk layar kecil. --}}
            <button
                type="button"
                @click="open = ! open"
                class="inline-flex items-center justify-center rounded-lg p-2 text-slate-500 transition hover:bg-slate-100 hover:text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 sm:hidden"
                :aria-expanded="open"
                aria-label="Buka menu navigasi"
            >
                <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path x-show="! open" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h16M4 18h16" />
                    <path x-show="open" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    {{-- Menu mobile dengan struktur yang sama seperti desktop. --}}
    <div
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="-translate-y-2 opacity-0"
        x-transition:enter-end="translate-y-0 opacity-100"
        class="border-t border-slate-200 bg-white sm:hidden"
    >
        <div class="space-y-1 px-4 py-3">
            @if (auth()->user()->role === \App\Enums\UserRole::Developer)
                <x-responsive-nav-link :href="route('developer.dashboard')" :active="request()->routeIs('developer.dashboard')">Dashboard</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('developer.tickets.index')" :active="request()->routeIs('developer.tickets.*')">Kelola Tiket</x-responsive-nav-link>
            @else
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">Dashboard</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('tickets.index')" :active="request()->routeIs('tickets.index', 'tickets.show', 'tickets.edit')">Tiket Saya</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('tickets.create')" :active="request()->routeIs('tickets.create')">Buat Tiket</x-responsive-nav-link>
            @endif
        </div>

        <div class="border-t border-slate-200 px-4 py-4">
            <div class="mb-3 flex items-center gap-3 px-3">
                <span class="flex size-9 items-center justify-center rounded-lg bg-indigo-100 font-bold uppercase text-indigo-700">
                    {{ mb_substr(auth()->user()->name, 0, 1) }}
                </span>
                <div class="min-w-0">
                    <p class="truncate text-sm font-semibold text-slate-900">{{ auth()->user()->name }}</p>
                    <p class="truncate text-xs text-slate-500">{{ auth()->user()->email }}</p>
                </div>
            </div>
            <x-responsive-nav-link :href="route('profile.edit')">Pengaturan Profil</x-responsive-nav-link>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-responsive-nav-link
                    :href="route('logout')"
                    onclick="event.preventDefault(); this.closest('form').submit();"
                >
                    Keluar
                </x-responsive-nav-link>
            </form>
        </div>
    </div>
</nav>
