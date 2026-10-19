<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    // Jika primary key bukan 'id', tentukan di sini
    protected $primaryKey = 'product_id';

    // Fields yang boleh diisi massal
    protected $fillable = [
        'name',
        'description',
        'category_id',
        'price',
        'img',
        'stock',
        'slug',
        'notes',
        'color',
        'rating',
    ];

    // Atribut tambahan yang otomatis disertakan saat model di-serialize ke JSON/Array
    protected $appends = [
        'id',
        'desc',
        'category_name',
        'img_url',
    ];

    /**
     * Relasi Many to One ke tabel categories (Bab III Modul)
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    // Accessor alias untuk $product->id -> product_id
    public function getIdAttribute()
    {
        return $this->attributes['product_id'] ?? null;
    }

    // Accessor alias untuk $product->desc -> description
    public function getDescAttribute()
    {
        return $this->attributes['description'] ?? '';
    }

    // Accessor alias untuk $product->category_name
    public function getCategoryNameAttribute()
    {
        return $this->category ? $this->category->category : '';
    }

    // Accessor untuk URL gambar publik yang selalu absolut (mencegah error relative URL di sub-path /produk/)
    public function getImgAttribute($value)
    {
        if (empty($value)) {
            return null;
        }

        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }

        if (str_starts_with($value, 'images/')) {
            return asset($value);
        }

        return url('storage/' . $value);
    }

    // Accessor img_url (sama dengan img yang telah diformat)
    public function getImgUrlAttribute()
    {
        return $this->img;
    }

    /**
     * Hapus berkas gambar fisik di disk public.
     * Memakai getRawOriginal('img') karena $product->img sudah diubah accessor menjadi URL.
     * Gambar bawaan (images/...) dan URL eksternal tidak dihapus.
     */
    public function deleteImageFile(): void
    {
        $path = $this->getRawOriginal('img');

        if ($path && !str_starts_with($path, 'images/') && !str_starts_with($path, 'http')
            && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    /**
     * Membuat slug unik: nama kembar menjadi nama-1, nama-2, dst.
     */
    public static function uniqueSlug(string $name, $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;

        while (static::where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->where('product_id', '!=', $ignoreId))
            ->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }
}
