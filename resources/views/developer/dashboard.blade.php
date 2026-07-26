<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Dashboard Developer
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Ringkasan antrean dan kondisi penanganan tiket SIPENA.
                </p>
            </div>

            <a
                href="{{ route('developer.tickets.index') }}"
                class="mt-3 inline-flex items-center justify-center rounded-md bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:mt-0"
            >
                Lihat Semua Tiket
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl space-y-8 sm:px-6 lg:px-8">
            {{-- Kartu ringkasan utama hasil satu query agregasi. --}}
            <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">Total Tiket</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ (int) $summary->total }}</p>
                    <p class="mt-2 text-xs text-gray-500">Seluruh laporan yang tercatat</p>
                </div>

                <div class="rounded-xl border border-red-200 bg-red-50 p-5 shadow-sm">
                    <p class="text-sm font-medium text-red-700">Prioritas Kritis</p>
                    <p class="mt-2 text-3xl font-bold text-red-900">{{ (int) $summary->critical }}</p>
                    <p class="mt-2 text-xs text-red-600">Memerlukan perhatian segera</p>
                </div>

                <div class="rounded-xl border border-amber-200 bg-amber-50 p-5 shadow-sm">
                    <p class="text-sm font-medium text-amber-700">Belum Ada PIC</p>
                    <p class="mt-2 text-3xl font-bold text-amber-900">{{ (int) $summary->unassigned }}</p>
                    <p class="mt-2 text-xs text-amber-600">Belum ditugaskan ke developer</p>
                </div>

                <div class="rounded-xl border border-purple-200 bg-purple-50 p-5 shadow-sm">
                    <p class="text-sm font-medium text-purple-700">Menunggu Konfirmasi</p>
                    <p class="mt-2 text-3xl font-bold text-purple-900">
                        {{ (int) $summary->status_waiting_confirmation }}
                    </p>
                    <p class="mt-2 text-xs text-purple-600">Solusi sudah dikirim ke reporter</p>
                </div>
            </section>

            {{-- Ringkasan jumlah tiket untuk setiap status workflow. --}}
            <section>
                <div class="mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Status Workflow</h3>
                    <p class="mt-1 text-sm text-gray-500">Distribusi tiket pada setiap tahap penanganan.</p>
                </div>

                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
                    @foreach (\App\Enums\TicketStatus::cases() as $status)
                        <a
                            href="{{ route('developer.tickets.index', ['status' => $status->value]) }}"
                            class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:border-indigo-200 hover:shadow"
                        >
                            <x-ticket-status-badge :status="$status" />
                            <p class="mt-3 text-2xl font-bold text-gray-900">
                                {{ (int) $summary->{'status_'.$status->value} }}
                            </p>
                        </a>
                    @endforeach
                </div>
            </section>

            {{-- Tabel ringkas tiket terbaru dan tiket mendesak. --}}
            <section class="grid gap-6 xl:grid-cols-2">
                <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-200 px-6 py-4">
                        <h3 class="font-semibold text-gray-900">Tiket Terbaru</h3>
                        <p class="mt-1 text-sm text-gray-500">Lima laporan terakhir yang masuk.</p>
                    </div>

                    @include('developer.tickets.partials.compact-table', [
                        'tickets' => $recentTickets,
                        'emptyMessage' => 'Belum ada tiket yang tercatat.',
                    ])
                </div>

                <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-200 px-6 py-4">
                        <h3 class="font-semibold text-gray-900">Prioritas Tinggi dan Kritis</h3>
                        <p class="mt-1 text-sm text-gray-500">Antrean yang perlu didahulukan.</p>
                    </div>

                    @include('developer.tickets.partials.compact-table', [
                        'tickets' => $urgentTickets,
                        'emptyMessage' => 'Tidak ada tiket prioritas tinggi atau kritis.',
                    ])
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
