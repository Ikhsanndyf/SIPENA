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
                    {{-- Ringkasan jumlah data pada tabel tiket reporter. --}}
                    <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                        <div>
                            <h3 class="font-semibold text-gray-900">Daftar Laporan Kendala</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Menampilkan {{ $tickets->firstItem() }}–{{ $tickets->lastItem() }}
                                dari {{ $tickets->total() }} tiket
                            </p>
                        </div>
                    </div>

                    {{-- Daftar tiket reporter dengan aksi pengelolaan. --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Nomor</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Kendala</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Aplikasi</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Prioritas</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Dibuat</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @foreach ($tickets as $ticket)
                                    <tr class="transition hover:bg-gray-50">
                                        <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                                            <a
                                                href="{{ route('tickets.show', $ticket) }}"
                                                class="font-semibold text-indigo-600 hover:text-indigo-800"
                                            >
                                                {{ $ticket->ticket_number }}
                                            </a>
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
                                            {{-- Badge prioritas memudahkan identifikasi urgensi. --}}
                                            <span @class([
                                                'inline-flex rounded-full px-2.5 py-1 text-xs font-semibold',
                                                'bg-gray-100 text-gray-700' => $ticket->priority->value === 'low',
                                                'bg-blue-100 text-blue-700' => $ticket->priority->value === 'medium',
                                                'bg-orange-100 text-orange-700' => $ticket->priority->value === 'high',
                                                'bg-red-100 text-red-700' => $ticket->priority->value === 'critical',
                                            ])>
                                                {{ ucfirst($ticket->priority->value) }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700">
                                            {{-- Badge status menunjukkan posisi tiket pada workflow. --}}
                                            <span @class([
                                                'inline-flex rounded-full px-2.5 py-1 text-xs font-semibold',
                                                'bg-gray-100 text-gray-700' => $ticket->status->value === 'new',
                                                'bg-blue-100 text-blue-700' => $ticket->status->value === 'analyzed',
                                                'bg-amber-100 text-amber-700' => $ticket->status->value === 'in_progress',
                                                'bg-purple-100 text-purple-700' => $ticket->status->value === 'waiting_confirmation',
                                                'bg-green-100 text-green-700' => $ticket->status->value === 'resolved',
                                                'bg-red-100 text-red-700' => $ticket->status->value === 'rejected',
                                            ])>
                                                {{ ucwords(str_replace('_', ' ', $ticket->status->value)) }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                            <div>{{ $ticket->created_at->format('d M Y') }}</div>
                                            <div class="mt-1 text-xs text-gray-400">
                                                {{ $ticket->created_at->format('H:i') }}
                                            </div>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                                            {{-- Aksi tabel ditampilkan sesuai hak akses dari Policy. --}}
                                            <div class="flex items-center justify-end gap-2">
                                                <a
                                                    href="{{ route('tickets.show', $ticket) }}"
                                                    class="inline-flex rounded-md border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700 transition hover:bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1"
                                                >
                                                    Detail
                                                </a>

                                                @can('update', $ticket)
                                                    <a
                                                        href="{{ route('tickets.edit', $ticket) }}"
                                                        class="inline-flex rounded-md border border-gray-300 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 transition hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-1"
                                                    >
                                                        Edit
                                                    </a>
                                                @endcan

                                                @can('delete', $ticket)
                                                    <form
                                                        method="POST"
                                                        action="{{ route('tickets.destroy', $ticket) }}"
                                                        onsubmit="return confirm('Hapus tiket {{ $ticket->ticket_number }}? Tindakan ini tidak dapat dibatalkan.')"
                                                    >
                                                        @csrf
                                                        @method('DELETE')

                                                        <button
                                                            type="submit"
                                                            class="inline-flex rounded-md border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 transition hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1"
                                                        >
                                                            Hapus
                                                        </button>
                                                    </form>
                                                @endcan
                                            </div>
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
