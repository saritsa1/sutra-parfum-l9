<x-admin-layout :title="$title">
    {{-- Halaman admin membaca data yang diinput pengguna di toko (pesanan dan ulasan) --}}
    <div class="max-w-4xl" x-data="adminDashboard(@js($products))">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="font-serif text-4xl">Dashboard</h1>
            <button type="button" x-on:click="reset()" class="btn btn-ghost px-4 py-2 text-sm"><x-icon name="rotate-ccw" :size="15" />Reset data</button>
        </div>

        <div class="mb-10 grid grid-cols-2 gap-4 md:grid-cols-4">
            <template x-for="s in stats" :key="s[0]">
                <div class="rounded-xl border border-line bg-white p-4">
                    <p class="text-sm text-mute" x-text="s[0]"></p>
                    <p class="font-serif text-3xl" x-text="s[1]"></p>
                </div>
            </template>
        </div>

        <h2 class="mb-3 font-serif text-2xl">Pesanan masuk</h2>
        <template x-if="orders.length === 0">
            <p class="mb-10 text-mute">Belum ada pesanan. Pesanan dari halaman checkout akan muncul di sini.</p>
        </template>
        <template x-if="orders.length > 0">
            <div class="mb-10 overflow-x-auto rounded-xl border border-line bg-white">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-line text-mute">
                        <tr><th class="p-3">Tanggal</th><th class="p-3">Pembeli</th><th class="p-3">Item</th><th class="p-3">Total</th><th class="p-3">Status</th></tr>
                    </thead>
                    <tbody>
                        <template x-for="o in orders" :key="o.id">
                            <tr class="border-b border-line last:border-0">
                                <td class="p-3" x-text="o.tanggal"></td>
                                <td class="p-3"><span x-text="o.nama"></span><br><span class="text-mute" x-text="o.bayar"></span></td>
                                <td class="p-3" x-text="o.items.map((i) => i.name + ' x' + i.qty).join(', ')"></td>
                                <td class="p-3" x-text="sutra.rupiah(o.total)"></td>
                                <td class="p-3">
                                    <select x-model="o.status" x-on:change="save()" :aria-label="'Status pesanan ' + o.nama" class="rounded border border-line bg-white p-1 focus:border-line">
                                        @foreach ($statuses as $s)
                                            <option>{{ $s }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </template>

        <h2 class="mb-3 font-serif text-2xl">Ulasan pelanggan</h2>
        <template x-if="reviews.length === 0">
            <p class="text-mute">Belum ada ulasan.</p>
        </template>
        <ul class="space-y-3" x-show="reviews.length > 0">
            <template x-for="r in reviews" :key="r.id">
                <li class="rounded-xl border border-line bg-white p-4">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <span class="font-medium" x-text="r.produk"></span><x-stars expr="r.rating" />
                    </div>
                    <p class="text-sm text-mute" x-text="r.nama + ', ' + r.tanggal"></p>
                    <p x-text="r.review"></p>
                </li>
            </template>
        </ul>
    </div>
</x-admin-layout>