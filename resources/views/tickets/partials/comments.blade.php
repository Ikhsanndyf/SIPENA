<section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
    <div class="border-b border-gray-200 px-6 py-4">
        <h3 class="font-semibold text-gray-900">Diskusi Tiket</h3>
        <p class="mt-1 text-sm text-gray-500">
            Reporter dan developer dapat bertukar informasi penanganan.
        </p>
    </div>

    {{-- Form komentar tidak menyediakan perubahan atau penghapusan. --}}
    <form method="POST" action="{{ $commentAction }}" class="border-b border-gray-100 p-6">
        @csrf

        <x-input-label for="body" value="Komentar Baru" />
        <textarea
            id="body"
            name="body"
            rows="4"
            maxlength="2000"
            required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            placeholder="Tuliskan informasi tambahan, pertanyaan, atau hasil pemeriksaan..."
        >{{ old('body') }}</textarea>
        <div class="mt-2 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <x-input-error :messages="$errors->get('body')" />
                <p class="mt-1 text-xs text-gray-500">Minimal 2 dan maksimal 2000 karakter.</p>
            </div>
            <x-primary-button type="submit">Kirim Komentar</x-primary-button>
        </div>
    </form>

    {{-- Daftar komentar memuat author lebih awal untuk mencegah N+1 query. --}}
    <div class="divide-y divide-gray-100">
        @forelse ($comments as $comment)
            <article class="p-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <p class="text-sm font-semibold text-gray-900">{{ $comment->user->name }}</p>
                            <span @class([
                                'rounded-full px-2 py-0.5 text-xs font-semibold',
                                'bg-indigo-100 text-indigo-700' => $comment->user->role === \App\Enums\UserRole::Developer,
                                'bg-gray-100 text-gray-700' => $comment->user->role === \App\Enums\UserRole::Reporter,
                            ])>
                                {{ $comment->user->role === \App\Enums\UserRole::Developer ? 'Developer' : 'Reporter' }}
                            </span>
                        </div>
                        <time class="mt-1 block text-xs text-gray-400">
                            {{ $comment->created_at->format('d M Y H:i') }}
                        </time>
                    </div>
                </div>
                <p class="mt-3 whitespace-pre-line text-sm leading-6 text-gray-700">{{ $comment->body }}</p>
            </article>
        @empty
            <div class="p-8 text-center text-sm text-gray-500">
                Belum ada komentar pada tiket ini.
            </div>
        @endforelse
    </div>

    {{-- Pagination komentar memakai parameter comments_page yang terpisah. --}}
    @if ($comments->hasPages())
        <div class="border-t border-gray-200 px-6 py-4">
            {{ $comments->links() }}
        </div>
    @endif
</section>
