<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        Penanganan Tiket
                    </h2>
                    <x-ticket-status-badge :status="$ticket->status" />
                    <x-ticket-priority-badge :priority="$ticket->priority" />
                </div>
                <p class="mt-1 text-sm text-gray-500">{{ $ticket->ticket_number }}</p>
            </div>

            <a
                href="{{ route('developer.tickets.index') }}"
                class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
            >
                Kembali ke Daftar
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            {{-- Notifikasi hasil pembaruan developer. --}}
            @if (session('success'))
                <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid gap-6 xl:grid-cols-3">
                {{-- Informasi laporan dan riwayat berada pada kolom utama. --}}
                <div class="space-y-6 xl:col-span-2">
                    <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                        <div class="border-b border-gray-200 px-6 py-4">
                            <h3 class="font-semibold text-gray-900">Informasi Laporan</h3>
                            <p class="mt-1 text-sm text-gray-500">Data yang disampaikan reporter.</p>
                        </div>

                        <div class="p-6">
                            <dl class="grid gap-6 sm:grid-cols-2">
                                <div>
                                    <dt class="text-xs font-semibold uppercase tracking-wider text-gray-500">Judul</dt>
                                    <dd class="mt-2 text-sm font-medium text-gray-900">{{ $ticket->title }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-semibold uppercase tracking-wider text-gray-500">Aplikasi</dt>
                                    <dd class="mt-2 text-sm text-gray-900">{{ $ticket->application->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-semibold uppercase tracking-wider text-gray-500">Reporter</dt>
                                    <dd class="mt-2 text-sm text-gray-900">{{ $ticket->reporter->name }}</dd>
                                    <dd class="mt-1 text-xs text-gray-500">{{ $ticket->reporter->email }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-semibold uppercase tracking-wider text-gray-500">Kategori</dt>
                                    <dd class="mt-2 text-sm text-gray-900">{{ ucfirst($ticket->category->value) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-semibold uppercase tracking-wider text-gray-500">Dibuat</dt>
                                    <dd class="mt-2 text-sm text-gray-900">{{ $ticket->created_at->format('d M Y H:i') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-semibold uppercase tracking-wider text-gray-500">PIC Saat Ini</dt>
                                    <dd class="mt-2 text-sm text-gray-900">{{ $ticket->assignee?->name ?? 'Belum ditentukan' }}</dd>
                                </div>
                            </dl>
                        </div>
                    </section>

                    {{-- Detail kendala dan langkah reproduksi. --}}
                    <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                        <div class="space-y-6 p-6">
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900">Deskripsi Kendala</h3>
                                <p class="mt-3 whitespace-pre-line text-sm leading-6 text-gray-700">{{ $ticket->description }}</p>
                            </div>

                            <div class="border-t border-gray-100 pt-6">
                                <h3 class="text-sm font-semibold text-gray-900">Langkah Reproduksi</h3>
                                <p class="mt-3 whitespace-pre-line text-sm leading-6 text-gray-700">
                                    {{ $ticket->reproduction_steps ?: 'Reporter tidak mencantumkan langkah reproduksi.' }}
                                </p>
                            </div>

                            @if ($ticket->attachment)
                                <div class="border-t border-gray-100 pt-6">
                                    <h3 class="text-sm font-semibold text-gray-900">Lampiran</h3>
                                    <a
                                        href="{{ asset('storage/'.$ticket->attachment->file_path) }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="mt-3 inline-flex items-center rounded-md bg-indigo-50 px-3 py-2 text-sm font-semibold text-indigo-700 hover:bg-indigo-100"
                                    >
                                        {{ $ticket->attachment->original_name }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    </section>

                    {{-- Timeline status terbaru ditampilkan lebih dahulu. --}}
                    <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                        <div class="border-b border-gray-200 px-6 py-4">
                            <h3 class="font-semibold text-gray-900">Riwayat Status</h3>
                            <p class="mt-1 text-sm text-gray-500">Jejak workflow yang tidak dapat diedit.</p>
                        </div>

                        <div class="p-6">
                            @forelse ($ticket->statusHistories as $history)
                                <div class="relative border-s-2 border-gray-200 pb-6 ps-6 last:border-transparent last:pb-0">
                                    <span class="absolute -start-[7px] top-1 h-3 w-3 rounded-full bg-indigo-600 ring-4 ring-white"></span>
                                    <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                        <div>
                                            <div class="flex flex-wrap items-center gap-2">
                                                @if ($history->from_status)
                                                    <x-ticket-status-badge :status="$history->from_status" />
                                                    <span class="text-xs text-gray-400">→</span>
                                                @endif
                                                <x-ticket-status-badge :status="$history->to_status" />
                                            </div>
                                            <p class="mt-2 text-sm text-gray-700">
                                                Oleh <span class="font-semibold">{{ $history->changedBy->name }}</span>
                                            </p>
                                            @if ($history->notes)
                                                <p class="mt-1 text-sm text-gray-500">{{ $history->notes }}</p>
                                            @endif
                                        </div>
                                        <time class="whitespace-nowrap text-xs text-gray-400">
                                            {{ $history->created_at->format('d M Y H:i') }}
                                        </time>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500">Belum ada riwayat status.</p>
                            @endforelse
                        </div>
                    </section>
                </div>

                {{-- Kontrol penanganan developer berada pada panel samping. --}}
                <aside class="space-y-6">
                    <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                        <div class="border-b border-gray-200 px-6 py-4">
                            <h3 class="font-semibold text-gray-900">Data Penanganan</h3>
                            <p class="mt-1 text-sm text-gray-500">Atur PIC, prioritas, analisis, dan solusi.</p>
                        </div>

                        <form method="POST" action="{{ route('developer.tickets.handling', $ticket) }}" class="space-y-5 p-6">
                            @csrf
                            @method('PATCH')

                            {{-- Penanggung jawab dan prioritas tiket. --}}
                            <div>
                                <x-input-label for="assigned_to" value="PIC Developer" />
                                <select id="assigned_to" name="assigned_to" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Belum ditentukan</option>
                                    @foreach ($developers as $developer)
                                        <option value="{{ $developer->id }}" @selected((string) old('assigned_to', $ticket->assigned_to) === (string) $developer->id)>
                                            {{ $developer->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('assigned_to')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="priority" value="Prioritas" />
                                <select id="priority" name="priority" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @foreach ($priorities as $priority)
                                        <option value="{{ $priority->value }}" @selected(old('priority', $ticket->priority->value) === $priority->value)>
                                            {{ ucfirst($priority->value) }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('priority')" class="mt-2" />
                            </div>

                            {{-- Catatan analisis dan solusi developer. --}}
                            <div>
                                <x-input-label for="analysis_notes" value="Catatan Analisis" />
                                <textarea id="analysis_notes" name="analysis_notes" rows="5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('analysis_notes', $ticket->analysis_notes) }}</textarea>
                                <x-input-error :messages="$errors->get('analysis_notes')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="resolution_notes" value="Catatan Solusi" />
                                <textarea id="resolution_notes" name="resolution_notes" rows="5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('resolution_notes', $ticket->resolution_notes) }}</textarea>
                                <p class="mt-1 text-xs text-gray-500">Wajib disimpan sebelum memilih status menunggu konfirmasi.</p>
                                <x-input-error :messages="$errors->get('resolution_notes')" class="mt-2" />
                            </div>

                            <x-primary-button type="submit" class="w-full justify-center">
                                Simpan Penanganan
                            </x-primary-button>
                        </form>
                    </section>

                    {{-- Form workflow dipisahkan agar transisi status tetap eksplisit. --}}
                    <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                        <div class="border-b border-gray-200 px-6 py-4">
                            <h3 class="font-semibold text-gray-900">Perubahan Status</h3>
                            <p class="mt-1 text-sm text-gray-500">Status saat ini mengikuti workflow SIPENA.</p>
                        </div>

                        <div class="p-6">
                            <div class="mb-5">
                                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Status saat ini</p>
                                <x-ticket-status-badge :status="$ticket->status" class="mt-2" />
                            </div>

                            @if ($allowedTransitions !== [])
                                <form method="POST" action="{{ route('developer.tickets.status', $ticket) }}" class="space-y-5">
                                    @csrf
                                    @method('PATCH')

                                    <div>
                                        <x-input-label for="status" value="Status Berikutnya" />
                                        <select id="status" name="status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="">Pilih status</option>
                                            @foreach ($allowedTransitions as $transition)
                                                <option value="{{ $transition->value }}" @selected(old('status') === $transition->value)>
                                                    {{ ucwords(str_replace('_', ' ', $transition->value)) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <x-input-error :messages="$errors->get('status')" class="mt-2" />
                                    </div>

                                    <div>
                                        <x-input-label for="notes" value="Catatan Perubahan (opsional)" />
                                        <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes') }}</textarea>
                                        <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                                    </div>

                                    <x-primary-button type="submit" class="w-full justify-center">
                                        Perbarui Status
                                    </x-primary-button>
                                </form>
                            @else
                                <div class="rounded-lg bg-gray-50 p-4 text-sm text-gray-600">
                                    Tidak ada transisi developer yang tersedia untuk status ini.
                                </div>
                            @endif
                        </div>
                    </section>
                </aside>
            </div>
        </div>
    </div>
</x-app-layout>
