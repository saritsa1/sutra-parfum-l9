<!-- memanggil admin template (Bab III Modul) -->
<x-admin-layout :title="$title">
    <div class="max-w-4xl">
        <div class="mb-6 flex items-center justify-between">
            <!-- menampilkan variabel title yang dikirim dari controller -->
            <h1 class="font-serif text-4xl">{{ $title }}</h1>
            <a href="{{ route('product.index') }}" class="btn btn-ghost px-4 py-2 text-sm">
                &larr; Kembali ke Daftar
            </a>
        </div>

        <div class="rounded-xl border border-line bg-white shadow-sm p-6">
            <!-- menggunakan route('product.update') jika terdapat $product, menggunakan route('product.store') untuk tambah data baru -->
            <!-- enctype='multipart/form-data' digunakan jika terdapat upload file -->
            <form enctype="multipart/form-data" action="{{ isset($product) ? route('product.update', $product->product_id) : route('product.store') }}" method="POST">
                <!-- generate token csrf -->
                @csrf

                <!-- menggunakan method PUT untuk update jika ditemukan $product -->
                @if (isset($product))
                    @method('PUT')
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nama Produk -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-ink mb-1">Nama Produk</label>
                        <input type="text" id="name" name="name" placeholder="Nama Produk Lengkap" required
                            value="{{ isset($product) ? $product->name : old('name') }}"
                            class="w-full rounded-lg border border-line bg-white px-4 py-2 text-sm focus:border-line">
                        @error('name')
                            <div class="mt-1 text-xs text-red-800">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Kategori Produk (Relasi Eloquent belongsTo) -->
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-ink mb-1">Kategori Produk</label>
                        <select id="category_id" name="category_id" required
                            class="w-full rounded-lg border border-line bg-white px-4 py-2 text-sm focus:border-line">
                            <option value="">Pilih Kategori</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->category_id }}"
                                    {{ ((isset($product) && $product->category_id == $cat->category_id) || old('category_id') == $cat->category_id) ? 'selected' : '' }}>
                                    {{ $cat->category }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="mt-1 text-xs text-red-800">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Harga Produk -->
                    <div>
                        <label for="price" class="block text-sm font-medium text-ink mb-1">Harga (Rp)</label>
                        <input type="number" id="price" name="price" placeholder="450000" required step="1"
                            value="{{ isset($product) ? (int)$product->price : old('price') }}"
                            class="w-full rounded-lg border border-line bg-white px-4 py-2 text-sm focus:border-line">
                        @error('price')
                            <div class="mt-1 text-xs text-red-800">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Stok Produk -->
                    <div>
                        <label for="stock" class="block text-sm font-medium text-ink mb-1">Jumlah Stok</label>
                        <input type="number" id="stock" name="stock" placeholder="10" required min="0"
                            value="{{ isset($product) ? $product->stock : old('stock', 0) }}"
                            class="w-full rounded-lg border border-line bg-white px-4 py-2 text-sm focus:border-line">
                        @error('stock')
                            <div class="mt-1 text-xs text-red-800">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Aroma / Catatan Notes -->
                    <div>
                        <label for="notes" class="block text-sm font-medium text-ink mb-1">Catatan Aroma / Spesifikasi</label>
                        <input type="text" id="notes" name="notes" placeholder="Mawar, peony, musk"
                            value="{{ isset($product) ? $product->notes : old('notes') }}"
                            class="w-full rounded-lg border border-line bg-white px-4 py-2 text-sm focus:border-line">
                        @error('notes')
                            <div class="mt-1 text-xs text-red-800">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Foto / Gambar Produk (Upload File) -->
                    <div>
                        <label for="img" class="block text-sm font-medium text-ink mb-1">Foto Produk</label>
                        <input type="file" id="img" name="img" accept="image/*"
                            class="w-full rounded-lg border border-line bg-white px-4 py-1.5 text-sm file:mr-4 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-soft file:text-ink hover:file:bg-line">
                        @if (isset($product) && $product->img)
                            <div class="mt-2 flex items-center gap-2 text-xs text-mute">
                                <span>File saat ini:</span>
                                <img src="{{ $product->img_url ?? asset('images/' . $product->img) }}" alt="" class="h-8 w-8 rounded object-cover border border-line">
                                <span>{{ basename($product->img) }}</span>
                            </div>
                        @endif
                        @error('img')
                            <div class="mt-1 text-xs text-red-800">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Deskripsi Produk -->
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-ink mb-1">Deskripsi Produk</label>
                        <textarea id="description" name="description" rows="3" placeholder="Tuliskan keterangan lengkap produk di sini..."
                            class="w-full rounded-lg border border-line bg-white px-4 py-2 text-sm focus:border-line">{{ isset($product) ? $product->description : old('description') }}</textarea>
                        @error('description')
                            <div class="mt-1 text-xs text-red-800">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <hr class="my-6 border-line">

                <!-- Tombol Simpan -->
                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('product.index') }}" class="btn btn-ghost px-5 py-2 text-sm">Batal</a>
                    <button type="submit" class="btn btn-solid px-6 py-2 text-sm">
                        {{ isset($product) ? 'Update' : 'Save' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
