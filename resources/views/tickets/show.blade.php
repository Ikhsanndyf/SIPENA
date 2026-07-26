<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Detail Tiket
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ $ticket->ticket_number }}
                </p>
            </div>

            {{-- Aksi tiket yang tersedia sesuai Policy. --}}
            <div class="flex items-center gap-4">
                @can('update', $ticket)
                    <a
                        href="{{ route('tickets.edit', $ticket) }}"
                        class="inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        Edit Tiket
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

                        <x-danger-button type="submit">
                            Hapus Tiket
                        </x-danger-button>
                    </form>
                @endcan

                <a
                    href="{{ route('tickets.index') }}"
                    class="text-sm font-medium text-indigo-600 hover:text-indigo-800"
                >
                    Kembali ke Daftar
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-5xl space-y-6 sm:px-6 lg:px-8">
            {{-- Menampilkan notifikasi setelah tiket diperbarui. --}}
            @if (session('success'))
                <div class="rounded-md border border-green-200 bg-green-50 p-4 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Informasi utama tiket. --}}
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <dl class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Judul</dt>
                            <dd class="mt-1 text-gray-900">{{ $ticket->title }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Aplikasi</dt>
                            <dd class="mt-1 text-gray-900">{{ $ticket->application->name }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Kategori</dt>
                            <dd class="mt-1 text-gray-900">{{ ucfirst($ticket->category->value) }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Prioritas</dt>
                            <dd class="mt-1 text-gray-900">{{ ucfirst($ticket->priority->value) }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="mt-1 text-gray-900">
                                {{ ucwords(str_replace('_', ' ', $ticket->status->value)) }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Reporter</dt>
                            <dd class="mt-1 text-gray-900">{{ $ticket->reporter->name }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">PIC</dt>
                            <dd class="mt-1 text-gray-900">
                                {{ $ticket->assignee?->name ?? 'Belum ditentukan' }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Dibuat</dt>
                            <dd class="mt-1 text-gray-900">
                                {{ $ticket->created_at->format('d M Y H:i') }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            {{-- Detail kendala yang disampaikan reporter. --}}
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="space-y-6 p-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Deskripsi</h3>
                        <p class="mt-2 whitespace-pre-line text-gray-900">{{ $ticket->description }}</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Langkah Reproduksi</h3>
                        <p class="mt-2 whitespace-pre-line text-gray-900">
                            {{ $ticket->reproduction_steps ?: 'Tidak dicantumkan.' }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Lampiran bukti kendala. --}}
            @if ($ticket->attachment)
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-sm font-medium text-gray-500">Lampiran</h3>
                        <a
                            href="{{ asset('storage/'.$ticket->attachment->file_path) }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="mt-2 inline-block text-sm font-medium text-indigo-600 hover:text-indigo-800"
                        >
                            {{ $ticket->attachment->original_name }}
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
