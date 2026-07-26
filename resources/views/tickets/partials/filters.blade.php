<form
    method="GET"
    action="{{ $filterAction }}"
    class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm"
>
    {{-- Pencarian utama berdasarkan nomor, judul, atau reporter. --}}
    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="md:col-span-2">
            <x-input-label for="search" value="Pencarian" />
            <x-text-input
                id="search"
                name="search"
                type="search"
                class="mt-1 block w-full"
                :value="request('search')"
                placeholder="Nomor tiket, judul, atau nama reporter"
            />
            <x-input-error :messages="$errors->get('search')" class="mt-2" />
        </div>

        {{-- Filter berdasarkan data domain tiket. --}}
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
            <x-input-error :messages="$errors->get('status')" class="mt-2" />
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
            <x-input-error :messages="$errors->get('priority')" class="mt-2" />
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
            <x-input-error :messages="$errors->get('application_id')" class="mt-2" />
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
            <x-input-error :messages="$errors->get('category')" class="mt-2" />
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
            <x-input-error :messages="$errors->get('assigned_to')" class="mt-2" />
        </div>

        {{-- Rentang tanggal pembuatan tiket. --}}
        <div>
            <x-input-label for="date_from" value="Tanggal Awal" />
            <x-text-input
                id="date_from"
                name="date_from"
                type="date"
                class="mt-1 block w-full"
                :value="request('date_from')"
            />
            <x-input-error :messages="$errors->get('date_from')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="date_to" value="Tanggal Akhir" />
            <x-text-input
                id="date_to"
                name="date_to"
                type="date"
                class="mt-1 block w-full"
                :value="request('date_to')"
            />
            <x-input-error :messages="$errors->get('date_to')" class="mt-2" />
        </div>
    </div>

    {{-- Aksi untuk menerapkan atau membersihkan seluruh filter. --}}
    <div class="mt-5 flex flex-wrap items-center justify-end gap-3 border-t border-gray-100 pt-5">
        <a
            href="{{ $resetUrl }}"
            class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
        >
            Reset
        </a>
        <x-primary-button type="submit">Terapkan Filter</x-primary-button>
    </div>
</form>
