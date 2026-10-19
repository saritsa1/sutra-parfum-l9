{{-- Kartu dua sisi: depan = gambar produk, belakang = keterangan dan aksi. Terbalik saat diklik. --}}
@props(['p'])
<article x-data="productCard(@js($p))" class="perspective">
    <div class="flip relative aspect-[3/4.3]" :class="flipped && 'is-flipped'">

        {{-- Sisi depan --}}
        <button type="button" x-effect="$el.inert = flipped" x-on:click="flipped = true" aria-label="Lihat info {{ $p->name }}"
            class="face absolute inset-0 flex flex-col overflow-hidden rounded-xl border border-line bg-white text-left shadow">
            <div class="min-h-0 flex-1"><x-product-image :p="$p" /></div>
            <div class="p-4">
                <div class="flex items-center justify-between text-xs text-mute">
                    <span>{{ $p->category_name }}</span>
                    <span class="flex items-center gap-1"><x-stars expr="rating.avg" :size="12" /><span x-text="rating.avg.toFixed(1)"></span></span>
                </div>
                {{-- text-xl: judul terpanjang tetap satu baris, jadi tinggi gambar tidak berubah-ubah --}}
                <h3 class="font-serif text-xl leading-tight">{{ $p->name }}</h3>
                <div class="mt-1 flex items-center justify-between">
                    <span class="font-medium">Rp {{ number_format($p->price, 0, ',', '.') }}</span>
                    <x-icon name="info" :size="18" class="text-mute" />
                </div>
            </div>
        </button>

        {{-- Sisi belakang --}}
        <div x-effect="$el.inert = !flipped" class="face face-back absolute inset-0 flex flex-col rounded-xl border border-line bg-white p-5 shadow">
            <div class="flex items-start justify-between">
                <span class="rounded-full bg-soft px-3 py-0.5 text-xs">{{ $p->category_name }}</span>
                <button type="button" x-on:click="flipped = false" aria-label="Balik kartu" class="text-mute hover:text-ink">
                    <x-icon name="rotate-ccw" :size="18" />
                </button>
            </div>
            <h3 class="mt-3 font-serif text-2xl leading-tight">{{ $p->name }}</h3>
            <p class="mt-1 text-sm text-mute">{{ $p->notes }}</p>
            <p class="mt-3 line-clamp-4 text-sm">{{ $p->desc }}</p>
            <p class="mt-auto pb-3 pt-2 text-sm text-mute">50 ml, stok {{ $p->stock }}</p>
            <div class="grid gap-2">
                <button type="button" x-on:click="buyNow()" class="btn btn-solid py-2 text-sm"><x-icon name="zap" :size="16" />Beli sekarang</button>
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" x-on:click="$store.cart.add(p)" class="btn btn-ghost px-3 py-2 text-sm"><x-icon name="shopping-bag" :size="16" />Keranjang</button>
                    <a href="{{ route('produk.detail', $p->slug) }}" class="btn btn-ghost px-3 py-2 text-sm"><x-icon name="eye" :size="16" />Detail</a>
                </div>
            </div>
        </div>
    </div>
</article>