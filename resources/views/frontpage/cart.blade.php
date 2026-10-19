<x-home-layout :title="$title">
    <div>
        {{-- Keranjang kosong --}}
        <template x-if="$store.cart.items.length === 0">
            <div>
                <h1 class="mb-3 font-serif text-4xl">Keranjang</h1>
                <p class="text-mute">Keranjang masih kosong. <a href="{{ route('home') }}" class="underline">Pilih parfum</a></p>
            </div>
        </template>

        {{-- Keranjang berisi --}}
        <template x-if="$store.cart.items.length > 0">
            <div>
                <h1 class="mb-6 font-serif text-4xl">Keranjang</h1>
                <ul class="border-t border-line">
                    <template x-for="i in $store.cart.items" :key="i.id">
                        <li class="flex flex-wrap items-center gap-4 border-b border-line py-4">
                            <div class="w-16">
                                <div class="aspect-square overflow-hidden rounded-md">
                                    <img :src="i.img" :alt="i.name" class="block h-full w-full bg-soft object-contain p-3">
                                </div>
                            </div>
                            <div class="flex-1">
                                <p class="font-serif text-xl" x-text="i.name"></p>
                                <p class="text-mute" x-text="sutra.rupiah(i.price)"></p>
                            </div>
                            <label class="text-sm">
                                <span class="sr-only" x-text="'Jumlah ' + i.name"></span>
                                <input type="number" min="1" :max="i.stock" :value="i.qty"
                                    x-on:input="$store.cart.updateQty(i.id, parseInt($event.target.value))"
                                    x-on:change="$el.value = i.qty"
                                    class="w-16 border border-mute/50 bg-white p-1 text-center focus:border-mute/50">
                            </label>
                            <button type="button" x-on:click="$store.cart.remove(i.id)" :aria-label="'Hapus ' + i.name" class="p-2 text-mute hover:text-red-700"><x-icon name="trash-2" :size="18" /></button>
                        </li>
                    </template>
                </ul>
                <div class="mt-6 flex flex-wrap items-center justify-between gap-4">
                    <p class="text-xl">Total: <strong class="font-medium" x-text="sutra.rupiah($store.cart.totalPrice)"></strong></p>
                    <a href="{{ route('checkout') }}" class="btn btn-solid">Lanjut ke checkout<x-icon name="arrow-right" :size="18" /></a>
                </div>
            </div>
        </template>
    </div>
</x-home-layout>