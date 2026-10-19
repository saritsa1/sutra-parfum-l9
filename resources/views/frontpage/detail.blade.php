<x-home-layout :title="$title">
    <div x-data="detailPage(@js($p))">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-1 text-mute hover:text-ink"><x-icon name="arrow-left" :size="16" />Kembali ke koleksi</a>

        <div class="mt-6 grid gap-12 md:grid-cols-2">
            <div class="aspect-square overflow-hidden rounded-xl border border-line shadow"><x-product-image :p="$p" /></div>
            <div>
                <h1 class="font-serif text-5xl">{{ $p->name }}</h1>
                <p class="mt-1 flex items-center gap-2 text-sm text-mute">
                    <x-stars expr="avg" :size="16" /><span x-text="avg.toFixed(1) + ' (' + reviews.length + ' ulasan)'"></span>
                </p>
                <p class="my-2 text-2xl">Rp {{ number_format($p->price, 0, ',', '.') }}</p>
                <p>{{ $p->desc }}</p>
                <dl class="my-6 border-t border-line">
                    @foreach ([['Aroma', $p->notes], ['Golongan', $p->category_name], ['Ukuran', '50 ml'], ['Stok', $p->stock]] as [$k, $v])
                        <div class="flex justify-between border-b border-line py-2"><dt class="text-mute">{{ $k }}</dt><dd>{{ $v }}</dd></div>
                    @endforeach
                </dl>
                <div class="flex flex-wrap items-center gap-3">
                    <div class="flex items-center rounded-full border border-line">
                        <button type="button" x-on:click="qty = Math.max(1, qty - 1)" aria-label="Kurangi jumlah" class="p-3 hover:text-acc"><x-icon name="minus" :size="16" /></button>
                        <span class="w-8 text-center" aria-live="polite" x-text="qty"></span>
                        <button type="button" x-on:click="qty = Math.min(p.stock, qty + 1)" aria-label="Tambah jumlah" class="p-3 hover:text-acc"><x-icon name="plus" :size="16" /></button>
                    </div>
                    <button type="button" x-on:click="$store.cart.add(p, qty)" class="btn btn-ghost"><x-icon name="shopping-bag" :size="18" />Tambah ke keranjang</button>
                    <button type="button" x-on:click="buyNow()" class="btn btn-solid"><x-icon name="zap" :size="18" />Beli sekarang</button>
                </div>
            </div>
        </div>

        <section class="mt-14 grid gap-10 md:grid-cols-2">
            <div>
                <h2 class="mb-4 font-serif text-3xl">Ulasan</h2>
                <template x-if="reviews.length === 0">
                    <p class="text-mute">Belum ada ulasan. Jadilah yang pertama memberi rating.</p>
                </template>
                <ul class="space-y-4" x-show="reviews.length > 0">
                    <template x-for="r in reviews" :key="r.id">
                        <li class="rounded-xl border border-line bg-white p-4">
                            <div class="flex items-center justify-between">
                                <x-stars expr="r.rating" />
                                <button type="button" x-on:click="remove(r.id)" aria-label="Hapus ulasan" class="text-mute hover:text-red-700"><x-icon name="trash-2" :size="16" /></button>
                            </div>
                            <p class="text-sm text-mute" x-text="r.nama + ', ' + r.tanggal"></p>
                            <p class="mt-1" x-text="r.review"></p>
                        </li>
                    </template>
                </ul>
            </div>

            <form x-on:submit.prevent="submit()" novalidate>
                <h2 class="mb-4 font-serif text-3xl">Beri rating</h2>
                <div class="mb-4 flex gap-1" role="group" aria-label="Rating">
                    <template x-for="s in 5" :key="s">
                        <button type="button" x-on:click="rating = s" :aria-label="s + ' bintang'" :aria-pressed="s <= rating"
                            class="transition-transform hover:scale-110" :class="s <= rating ? 'text-acc' : 'text-line'">
                            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" :class="s <= rating ? 'fill-current' : ''">
                                <path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z" />
                            </svg>
                        </button>
                    </template>
                </div>
                <label for="nama" class="mb-1 block font-medium">Nama (opsional)</label>
                <input id="nama" type="text" x-model="nama" class="field mb-4">
                <label for="review" class="mb-1 block font-medium">Pengalaman Anda</label>
                <textarea id="review" rows="4" x-model="review" class="field mb-2"></textarea>
                <small class="mb-2 block min-h-[1.25rem] text-red-700" role="alert" x-text="error"></small>
                <button type="submit" class="btn btn-solid">Kirim ulasan</button>
            </form>
        </section>
    </div>
</x-home-layout>