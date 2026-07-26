<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Kelola Tiket
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Cari, prioritaskan, dan buka tiket yang perlu ditangani.
            </p>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            {{-- Filter tiket developer dengan query string yang tetap saat pagination. --}}
            <form
                method="GET"
                action="{{ route('developer.tickets.index') }}"
                class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm"
            >
                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                    <div class="md:col-span-2">
                        <x-input-label for="search" value="Pencarian" />
                        <x-text-input
                            id="search"
                            name="search"
                            type="search"
                            class="mt-1 block w-full"
                            :value="request('search')"
                            placeholder="Nomor tiket, judul, atau reporter"
                        />
                    </div>

                    <div>
                        <x-input-label for="status" value="Status" />
                        <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Semua status</option>
                            @foreach ($statuses as $status)
                                <option value="{{ $status->value }}" @selected(request('status') === $status->value)>
                                    {{ ucwords(str_replace('_', ' ', $status->value)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <x-input-label for="priority" value="Prioritas" />
                        <select id="priority" name="priority" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Semua prioritas</option>
                            @foreach ($priorities as $priority)
                                <option value="{{ $priority->value }}" @selected(request('priority') === $priority->value)>
                                    {{ ucfirst($priority->value) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <x-input-label for="application_id" value="Aplikasi" />
                        <select id="application_id" name="application_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Semua aplikasi</option>
                            @foreach ($applications as $application)
                                <option value="{{ $application->id }}" @selected((string) request('application_id') === (string) $application->id)>
                                    {{ $application->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <x-input-label for="category" value="Kategori" />
                        <select id="category" name="category" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Semua kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->value }}" @selected(request('category') === $category->value)>
                                    {{ ucfirst($category->value) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <x-input-label for="assigned_to" value="PIC Developer" />
                        <select id="assigned_to" name="assigned_to" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Semua PIC</option>
                            <option value="unassigned" @selected(request('assigned_to') === 'unassigned')>Belum ada PIC</option>
                            @foreach ($developers as $developer)
                                <option value="{{ $developer->id }}" @selected((string) request('assigned_to') === (string) $developer->id)>
                                    {{ $developer->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Aksi untuk menerapkan atau membersihkan filter. --}}
                    <div class="flex items-end gap-3">
                        <x-primary-button type="submit">Terapkan</x-primary-button>
                        <a
                            href="{{ route('developer.tickets.index') }}"
                            class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                            Reset
                        </a>
                    </div>
                </div>
            </form>

            {{-- Tabel seluruh tiket dengan relasi yang sudah di-eager load. --}}
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                    <div>
                        <h3 class="font-semibold text-gray-900">Daftar Tiket</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            {{ $tickets->total() }} tiket sesuai pencarian dan filter.
                        </p>
                    </div>
                </div>

                @if ($tickets->isEmpty())
                    <div class="px-6 py-12 text-center">
                        <h4 class="font-semibold text-gray-900">Tiket tidak ditemukan</h4>
                        <p class="mt-2 text-sm text-gray-500">Ubah atau reset filter untuk melihat data lainnya.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Tiket</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Reporter</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Aplikasi</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">PIC</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Prioritas</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @foreach ($tickets as $ticket)
                                    <tr class="transition hover:bg-gray-50">
                                        <td class="px-5 py-4">
                                            <p class="whitespace-nowrap text-sm font-semibold text-indigo-700">{{ $ticket->ticket_number }}</p>
                                            <p class="mt-1 max-w-xs truncate text-sm text-gray-700">{{ $ticket->title }}</p>
                                            <p class="mt-1 text-xs text-gray-400">{{ $ticket->created_at->format('d M Y H:i') }}</p>
                                        </td>
                                        <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-700">{{ $ticket->reporter->name }}</td>
                                        <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-700">{{ $ticket->application->name }}</td>
                                        <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-700">
                                            {{ $ticket->assignee?->name ?? 'Belum ditentukan' }}
                                        </td>
                                        <td class="whitespace-nowrap px-5 py-4">
                                            <x-ticket-priority-badge :priority="$ticket->priority" />
                                        </td>
                                        <td class="whitespace-nowrap px-5 py-4">
                                            <x-ticket-status-badge :status="$ticket->status" />
                                        </td>
                                        <td class="whitespace-nowrap px-5 py-4 text-right">
                                            <a
                                                href="{{ route('developer.tickets.show', $ticket) }}"
                                                class="inline-flex rounded-md border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700 transition hover:bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1"
                                            >
                                                Lihat & Tangani
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination mempertahankan seluruh parameter filter. --}}
                    <div class="border-t border-gray-200 px-6 py-4">
                        {{ $tickets->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
