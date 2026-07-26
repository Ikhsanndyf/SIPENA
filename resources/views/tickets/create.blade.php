<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Buat Tiket Kendala
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        {{-- Informasi utama kendala. --}}
                        <div>
                            <x-input-label for="application_id" value="Aplikasi" />
                            <select
                                id="application_id"
                                name="application_id"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="">Pilih aplikasi</option>
                                @foreach ($applications as $application)
                                    <option value="{{ $application->id }}" @selected(old('application_id') == $application->id)>
                                        {{ $application->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('application_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="title" value="Judul Kendala" />
                            <x-text-input
                                id="title"
                                name="title"
                                type="text"
                                class="mt-1 block w-full"
                                :value="old('title')"
                                required
                                autofocus
                            />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="category" value="Kategori" />
                            <select
                                id="category"
                                name="category"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="">Pilih kategori</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->value }}" @selected(old('category') === $category->value)>
                                        {{ ucfirst($category->value) }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('category')" class="mt-2" />
                        </div>

                        {{-- Detail dan langkah terjadinya kendala. --}}
                        <div>
                            <x-input-label for="description" value="Deskripsi Kendala" />
                            <textarea
                                id="description"
                                name="description"
                                rows="6"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="reproduction_steps" value="Langkah Reproduksi (opsional)" />
                            <textarea
                                id="reproduction_steps"
                                name="reproduction_steps"
                                rows="5"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >{{ old('reproduction_steps') }}</textarea>
                            <x-input-error :messages="$errors->get('reproduction_steps')" class="mt-2" />
                        </div>

                        {{-- Lampiran bukti kendala. --}}
                        <div>
                            <x-input-label for="attachment" value="Lampiran (opsional)" />
                            <input
                                id="attachment"
                                name="attachment"
                                type="file"
                                accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                                class="mt-1 block w-full text-sm text-gray-700 file:me-4 file:rounded-md file:border-0 file:bg-gray-100 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-gray-700 hover:file:bg-gray-200"
                            >
                            <p class="mt-1 text-sm text-gray-500">JPG, JPEG, PNG, atau WEBP. Maksimal 2 MB.</p>
                            <x-input-error :messages="$errors->get('attachment')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end gap-3">
                            <a
                                href="{{ route('dashboard') }}"
                                class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                            >
                                Batal
                            </a>
                            <x-primary-button>Simpan Tiket</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
