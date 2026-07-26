@if ($tickets->isEmpty())
    {{-- Kondisi tabel ringkas tanpa data. --}}
    <div class="px-6 py-10 text-center text-sm text-gray-500">
        {{ $emptyMessage }}
    </div>
@else
    {{-- Tabel ringkas untuk dashboard developer. --}}
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Tiket</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @foreach ($tickets as $ticket)
                    <tr class="transition hover:bg-gray-50">
                        <td class="px-5 py-4">
                            <p class="text-sm font-semibold text-gray-900">{{ $ticket->ticket_number }}</p>
                            <p class="mt-1 max-w-xs truncate text-sm text-gray-600">{{ $ticket->title }}</p>
                            <p class="mt-1 text-xs text-gray-400">{{ $ticket->application->name }}</p>
                        </td>
                        <td class="whitespace-nowrap px-5 py-4">
                            <div class="space-y-2">
                                <x-ticket-status-badge :status="$ticket->status" />
                                <div>
                                    <x-ticket-priority-badge :priority="$ticket->priority" />
                                </div>
                            </div>
                        </td>
                        <td class="whitespace-nowrap px-5 py-4 text-right">
                            <a
                                href="{{ route('developer.tickets.show', $ticket) }}"
                                class="text-sm font-semibold text-indigo-600 hover:text-indigo-800"
                            >
                                Tangani
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
