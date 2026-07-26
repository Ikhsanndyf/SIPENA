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
            {{-- Filter reusable menjaga perilaku reporter dan developer konsisten. --}}
            @include('tickets.partials.filters', [
                'filterAction' => route('developer.tickets.index'),
                'resetUrl' => route('developer.tickets.index'),
            ])

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
