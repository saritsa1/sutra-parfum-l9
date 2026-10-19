<x-home-layout :title="$title">
    @if ($q === '')
        <section class="grid items-center gap-10 pb-14 pt-4 md:grid-cols-[1.3fr_.7fr]">
            <div>
                <h1 class="rise mb-4 font-serif text-5xl leading-tight md:text-6xl md:leading-tight">
                    Wangi yang tenang, bertahan sepanjang hari.
                </h1>
                <p class="rise max-w-[46ch] text-lg text-mute" style="animation-delay: .15s">
                    Enam aroma pilihan dari bahan yang diracik perlahan. Tanpa ramai, hanya yang perlu.
                </p>
            </div>
        </section>
    @endif

    <h2 class="mb-4 font-serif text-3xl">{{ $q !== '' ? 'Hasil untuk "' . $q . '"' : 'Dashboard Produk' }}</h2>

    {{-- Filter golongan aroma: kategori disimpan di URL (?cat=...), pencarian di (?q=...) --}}
    <div class="mb-8 flex flex-wrap gap-2" role="group" aria-label="Filter golongan aroma">
        @foreach ($kategori as $c)
            <a href="{{ route('home', array_filter(['q' => $q !== '' ? $q : null, 'cat' => $c !== 'Semua' ? $c : null])) }}"
                class="rounded-full border px-4 py-1 text-sm transition-colors {{ $cat === $c ? 'border-ink bg-ink text-white' : 'border-line text-mute hover:border-ink hover:text-ink' }}">
                {{ $c }}
            </a>
        @endforeach
    </div>

    @if ($products->isEmpty())
        <div class="py-16 text-center text-mute">
            <x-icon name="search-x" :size="40" class="mx-auto mb-3" />
            <p>Tidak ada parfum yang cocok. Coba kata lain atau golongan lain.</p>
            <a href="{{ route('home') }}" class="btn btn-ghost mt-5">Tampilkan semua</a>
        </div>
    @else
        <div class="grid grid-cols-[repeat(auto-fill,minmax(210px,1fr))] gap-6">
            @foreach ($products as $item)
                <x-product-card :p="$item" />
            @endforeach
        </div>
    @endif
</x-home-layout>