<x-home-layout :title="$title">
    <div x-data="orderPage({{ (int) $id }})">

        <template x-if="!o">
            <p>Pesanan tidak ditemukan. <a href="{{ route('home') }}" class="underline">Kembali ke koleksi</a></p>
        </template>

        <template x-if="o">
            <div class="mx-auto max-w-2xl">
                <div class="mb-6 text-center">
                    <x-icon name="circle-check" :size="44" class="mx-auto mb-2 text-acc" />
                    <h1 class="font-serif text-4xl">Pesanan berhasil dibuat</h1>
                    <p class="text-mute">Nomor pesanan <strong class="font-medium text-ink" x-text="o.no"></strong>, <span x-text="o.tanggal"></span></p>
                    <span class="mt-2 inline-block rounded-full bg-soft px-4 py-1 text-sm" x-text="o.status"></span>
                </div>

                {{-- Instruksi pembayaran sesuai jenis metode --}}
                <section class="mb-6 rounded-xl border border-line bg-white p-5">
                    <h2 class="mb-2 font-serif text-2xl">Pembayaran</h2>
                    <p class="text-sm text-mute" x-text="o.bayar"></p>
                    <template x-if="o.tipe === 'va'">
                        <div class="mt-3">
                            <p class="text-sm">Nomor Virtual Account</p>
                            <p class="flex items-center gap-3 font-mono text-2xl tracking-wider">
                                <span x-text="o.va"></span>
                                <button type="button" x-on:click="copy()" class="btn btn-ghost px-3 py-1 text-sm"><x-icon name="copy" :size="14" /><span x-text="copied ? 'Tersalin' : 'Salin'"></span></button>
                            </p>
                        </div>
                    </template>
                    <template x-if="o.tipe === 'ewallet'">
                        <p class="mt-3" x-text="'Buka aplikasi ' + o.bayar + ' Anda, lalu selesaikan pembayaran sebesar ' + sutra.rupiah(o.total) + '.'"></p>
                    </template>
                    <template x-if="o.tipe === 'cod'">
                        <p class="mt-3" x-text="'Siapkan uang pas ' + sutra.rupiah(o.total) + ' saat paket tiba.'"></p>
                    </template>
                    <template x-if="menunggu">
                        <div>
                            <p class="mt-3 text-sm text-mute" x-text="'Bayar sebelum ' + o.batas"></p>
                            <button type="button" x-on:click="bayar()" class="btn btn-solid mt-4">Simulasikan pembayaran</button>
                        </div>
                    </template>
                </section>

                <section class="mb-6 rounded-xl border border-line bg-white p-5">
                    <h2 class="mb-2 font-serif text-2xl">Info pengiriman</h2>
                    <p x-text="o.nama + ', ' + o.telepon"></p>
                    <p class="text-mute" x-text="o.alamat"></p>
                    <p class="mt-2 text-sm" x-text="'Kurir: ' + o.kurir"></p>
                </section>

                <section class="mb-6 rounded-xl border border-line bg-white p-5">
                    <h2 class="mb-3 font-serif text-2xl">Rincian belanja</h2>
                    <ul class="mb-4 space-y-3 border-b border-line pb-4">
                        <template x-for="i in o.items" :key="i.id">
                            <li class="flex items-center gap-3 text-sm">
                                <img :src="i.img" alt="" class="h-12 w-12 rounded bg-soft object-contain">
                                <span class="flex-1"><span x-text="i.name"></span><br><span class="text-mute" x-text="i.qty + ' x ' + sutra.rupiah(i.price)"></span></span>
                                <span x-text="sutra.rupiah(i.qty * i.price)"></span>
                            </li>
                        </template>
                    </ul>
                    <x-price-summary subtotal="o.subtotal" ongkir="o.ongkir" layanan="o.layanan" biaya="o.biayaMetode" total="o.total" />
                </section>

                <div class="text-center"><a href="{{ route('home') }}" class="btn btn-ghost">Lanjut belanja</a></div>
            </div>
        </template>
    </div>
</x-home-layout>