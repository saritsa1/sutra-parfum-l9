<!-- memanggil admin template -->
<x-admin-layout :title="$title">
    <div class="max-w-5xl">
        <div class="mb-6 flex items-center justify-between">
            <!-- menampilkan variabel title yang dikirim dari controller -->
            <h1 class="font-serif text-4xl">{{ $title }}</h1>
        </div>

        @if (session('success'))
            <div class="mb-4 rounded-lg bg-green-100 p-4 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-wrap gap-2">
                <!-- menampilkan url untuk halaman input produk (Bab III) -->
                <a href="{{ route('product.create') }}" class="btn btn-solid px-4 py-2 text-sm">
                    <x-icon name="plus" :size="15" /> Tambah
                </a>
                <button type="button" class="btn btn-ghost px-4 py-2 text-sm">Publish</button>
                <button type="button" class="btn btn-ghost px-4 py-2 text-sm">Delete</button>
            </div>

            <!-- Form Pencarian dan Filter Kategori (Bab IV Modul) -->
            <form action="{{ route('product.index') }}" method="GET" class="flex flex-wrap items-center gap-2">
                <div>
                    <!-- Filter berupa kategori produk (Bab IV) -->
                    <select id="category_id" name="category_id" class="rounded-lg border border-line bg-white px-3 py-2 text-sm focus:border-line">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->category_id }}" {{ (isset($_GET['category_id']) && $_GET['category_id'] == $cat->category_id) ? 'selected' : '' }}>
                                {{ $cat->category }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex">
                    <!-- Kata kunci dengan parameter 's' (Bab IV) -->
                    <input type="text" name="s" value="{{ request('s', '') }}" placeholder="Cari produk" aria-label="Cari produk" class="rounded-l-lg border border-line bg-white px-4 py-2 text-sm focus:border-line">
                    <button type="submit" class="rounded-r-lg border border-l-0 border-line bg-white px-4 py-2 text-sm text-mute hover:bg-soft">Cari</button>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto rounded-xl border border-line bg-white">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-line text-mute">
                    <tr>
                        <th class="p-3">Nama</th>
                        <th class="p-3">Harga</th>
                        <th class="p-3">Kategori</th>
                        <th class="p-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- menggunakan foreach untuk menampilkan list array ataupun collection (Bab III & Bab IV) -->
                    @forelse ($products as $item)
                        <tr class="border-b border-line last:border-0">
                            <td class="p-3">
                                <div class="flex items-center gap-3">
                                    <input type="checkbox" name="" id="">
                                    <img src="{{ $item->img_url ?? asset('images/' . $item->img) }}" alt="{{ $item->name }}" class="h-10 w-10 rounded bg-soft object-contain" onerror="this.src='https://placehold.co/80x80?text=Produk'">
                                    <div>
                                        <div class="font-medium">{{ $item->name }}</div>
                                        <div class="text-mute text-xs">{{ $item->slug }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="p-3">
                                <div>Rp {{ number_format($item->price, 0, ',', '.') }}</div>
                                <div class="text-mute text-xs">Stok: {{ $item->stock }}</div>
                            </td>
                            <td class="p-3 text-mute">
                                @if ($item->category)
                                    <!-- Menampilkan relasi belongsTo dan link filter hasMany ke kategori (Bab IV) -->
                                    <a href="{{ route('product.category', $item->category->category_id) }}" class="text-acc hover:underline font-medium">
                                        {{ $item->category->category }}
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="p-3 text-right">
                                <form action="{{ route('product.destroy', $item->product_id ?? $item->id) }}" method="POST" class="inline-flex items-center gap-3">
                                    <!-- Form lengkap dengan token csrf untuk method(DELETE) (Bab III) -->
                                    @csrf
                                    @method('DELETE')
                                    <!-- link untuk edit -->
                                    <a href="{{ route('product.edit', $item->product_id ?? $item->id) }}" class="text-acc hover:underline">Edit</a>
                                    <!-- button action untuk delete -->
                                    <button type="submit" onclick="return confirm('Anda Yakin ?')" class="text-red-700 hover:underline">Del</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-6 text-center text-mute">
                                Tidak ada data produk yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINATION (Bab IV) -->
        <!-- Menampilkan pagination lengkap dengan parameter pencarian query string -->
        @if ($products instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="mt-4">
                {{ $products->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</x-admin-layout>