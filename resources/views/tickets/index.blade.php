<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Tiket Saya
            </h2>

            @can('create', \App\Models\Ticket::class)
                <a
                    href="{{ route('tickets.create') }}"
                    class="inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition hover:bg-gray-700 focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    Buat Tiket
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            {{-- Menampilkan notifikasi setelah proses tiket berhasil. --}}
            @if (session('success'))
                <div class="mb-6 rounded-md border border-green-200 bg-green-50 p-4 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                @if ($tickets->isEmpty())
                    {{-- Kondisi ketika reporter belum memiliki tiket. --}}
                    <div class="p-8 text-center">
                        <h3 class="text-lg font-semibold text-gray-800">Belum ada tiket</h3>
                        <p class="mt-2 text-sm text-gray-600">
                            Laporkan kendala aplikasi agar dapat ditangani oleh developer.
                        </p>
                        <a
                            href="{{ route('tickets.create') }}"
                            class="mt-5 inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                            Buat Tiket Pertama
                        </a>
                    </div>
                @else
                    {{-- Daftar tiket reporter pada layar desktop. --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Nomor</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Kendala</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Aplikasi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Prioritas</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Dibuat</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @foreach ($tickets as $ticket)
                                    <tr>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                                            {{ $ticket->ticket_number }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700">
                                            <div class="font-medium text-gray-900">{{ $ticket->title }}</div>
                                            <div class="mt-1 text-xs text-gray-500">
                                                {{ ucfirst($ticket->category->value) }}
                                            </div>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700">
                                            {{ $ticket->application->name }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700">
                                            {{ ucfirst($ticket->priority->value) }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700">
                                            {{ ucwords(str_replace('_', ' ', $ticket->status->value)) }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                            {{ $ticket->created_at->format('d M Y H:i') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Navigasi halaman dengan batas sepuluh tiket. --}}
                    <div class="border-t border-gray-200 px-6 py-4">
                        {{ $tickets->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
