<x-home-layout :title="$title">
    <div x-data="checkoutPage(@js(['shipping' => $shipping, 'groups' => $groups]))">

        {{-- Keranjang kosong --}}
        <template x-if="$store.cart.items.length === 0">
            <p>Keranjang kosong. <a href="{{ route('home') }}" class="underline">Pilih parfum dulu</a></p>
        </template>

        <template x-if="$store.cart.items.length > 0">
            <form x-on:submit.prevent="submit()" novalidate class="grid gap-8 md:grid-cols-[1fr_340px]">
                <div>
                    {{-- Tombol kembali: pengguna bisa membatalkan checkout tanpa kehilangan isi keranjang --}}
                    <a href="{{ route('cart') }}" class="mb-4 inline-flex items-center gap-1 text-mute hover:text-ink"><x-icon name="arrow-left" :size="16" />Kembali ke keranjang</a>
                    <h1 class="mb-6 font-serif text-4xl">Checkout</h1>

                    <h2 class="mb-3 font-serif text-2xl">Alamat pengiriman</h2>
                    <div class="mb-3">
                        <label for="nama" class="mb-1 block font-medium">Nama penerima</label>
                        <input id="nama" name="nama" type="text" x-model="form.nama" class="field">
                        <small class="block min-h-[1.25rem] text-red-700" x-text="errors.nama"></small>
                    </div>
                    <div class="mb-3">
                        <label for="telepon" class="mb-1 block font-medium">Nomor HP</label>
                        <input id="telepon" name="telepon" type="text" inputmode="numeric" x-model="form.telepon" class="field">
                        <small class="block min-h-[1.25rem] text-red-700" x-text="errors.telepon"></small>
                    </div>
                    <div class="mb-8">
                        <label for="alamat" class="mb-1 block font-medium">Alamat lengkap</label>
                        <textarea id="alamat" name="alamat" rows="3" x-model="form.alamat" class="field"></textarea>
                        <small class="block min-h-[1.25rem] text-red-700" x-text="errors.alamat"></small>
                    </div>

                    <fieldset class="mb-8">
                        <legend class="mb-3 font-serif text-2xl">Pengiriman</legend>
                        <div class="grid gap-2">
                            <template x-for="s in shipping" :key="s.id">
                                <label class="choice">
                                    <input type="radio" name="kurir" :value="s.id" x-model="shipId">
                                    <span class="flex-1"><span x-text="s.name"></span> <span class="text-sm text-mute" x-text="'estimasi ' + s.est"></span></span>
                                    <span x-text="sutra.rupiah(s.cost)"></span>
                                </label>
                            </template>
                        </div>
                    </fieldset>

                    <fieldset>
                        <legend class="mb-3 font-serif text-2xl">Metode pembayaran</legend>
                        <template x-for="g in groups" :key="g.group">
                            <div class="mb-4">
                                <p class="mb-2 text-sm text-mute" x-text="g.group"></p>
                                <div class="grid gap-2">
                                    <template x-for="m in g.methods" :key="m.id">
                                        <label class="choice">
                                            <input type="radio" name="bayar" :value="m.id" x-model="payId">
                                            <span class="flex-1" x-text="m.name"></span>
                                            <span x-show="m.fee > 0" class="text-sm text-mute" x-text="'+' + sutra.rupiah(m.fee)"></span>
                                        </label>
                                    </template>
                                </div>
                            </div>
                        </template>
                        <small class="block min-h-[1.25rem] text-red-700" x-text="errors.bayar"></small>
                    </fieldset>
                </div>

                {{-- Ringkasan belanja --}}
                <aside class="self-start rounded-xl border border-line bg-white p-5 md:sticky md:top-24">
                    <h2 class="mb-3 font-serif text-2xl">Rincian belanja</h2>
                    <ul class="mb-4 space-y-3 border-b border-line pb-4">
                        <template x-for="i in $store.cart.items" :key="i.id">
                            <li class="flex items-center gap-3 text-sm">
                                <img :src="i.img" alt="" class="h-12 w-12 rounded bg-soft object-contain">
                                <span class="flex-1"><span x-text="i.name"></span><br><span class="text-mute" x-text="i.qty + ' x ' + sutra.rupiah(i.price)"></span></span>
                                <span x-text="sutra.rupiah(i.qty * i.price)"></span>
                            </li>
                        </template>
                    </ul>
                    <x-price-summary subtotal="$store.cart.totalPrice" ongkir="ship.cost" layanan="SERVICE_FEE" biaya="biayaMetode" total="total" />
                    <button type="submit" class="btn btn-solid mt-5 w-full">Buat pesanan</button>
                    {{-- Tombol batal: kembali ke keranjang, isi keranjang tidak dihapus --}}
                    <a href="{{ route('cart') }}" class="btn btn-ghost mt-2 w-full">Batal</a>
                </aside>
            </form>
        </template>
    </div>
</x-home-layout>