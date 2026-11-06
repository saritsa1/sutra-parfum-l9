<?php

namespace App\Support;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Tempat menyimpan dan menghapus foto produk.
 *
 * - IMAGE_DRIVER=local    : disimpan di storage/app/public/products (cara bawaan, untuk komputer sendiri)
 * - IMAGE_DRIVER=supabase : diunggah ke Supabase Storage lewat HTTP (untuk Vercel)
 *
 * Nilai yang disimpan di kolom products.img:
 *   local    -> path, contoh "products/abc123.jpg"
 *   supabase -> URL lengkap gambar
 *   bawaan   -> "images/nama.webp" (file di public/images, tidak pernah dihapus)
 */
class ImageStorage
{
    public static function usesSupabase(): bool
    {
        return config('services.product_images.driver') === 'supabase';
    }

    // Simpan file upload, kembalikan nilai untuk kolom img
    public static function store(UploadedFile $file): string
    {
        if (!self::usesSupabase()) {
            return $file->store('products', 'public');
        }

        $cfg = config('services.supabase');
        $path = 'products/' . Str::random(20) . '.' . ($file->extension() ?: 'jpg');

        $res = Http::withHeaders(['Authorization' => 'Bearer ' . $cfg['key'], 'apikey' => $cfg['key']])
            ->withBody(file_get_contents($file->getRealPath()), $file->getMimeType())
            ->post("{$cfg['url']}/storage/v1/object/{$cfg['bucket']}/{$path}");

        if ($res->failed()) {
            throw new HttpResponseException(response()->json([
                'status'  => 'error',
                'message' => 'Gagal mengunggah gambar ke penyimpanan.',
            ], 502));
        }

        return self::publicUrl($path);
    }

    // Hapus file lama. $raw = nilai mentah kolom img (bukan hasil accessor)
    public static function delete(?string $raw): void
    {
        if (!$raw || str_starts_with($raw, 'images/')) {
            return; // kosong atau gambar bawaan
        }

        // Gambar di Supabase: ambil path-nya dari URL lalu hapus lewat API
        if (str_starts_with($raw, 'http')) {
            $cfg = config('services.supabase');
            $prefix = self::publicUrl('');
            if ($cfg['url'] && str_starts_with($raw, $prefix)) {
                Http::withHeaders(['Authorization' => 'Bearer ' . $cfg['key'], 'apikey' => $cfg['key']])
                    ->delete("{$cfg['url']}/storage/v1/object/{$cfg['bucket']}/" . substr($raw, strlen($prefix)));
            }
            return; // URL luar lain tidak disentuh
        }

        if (Storage::disk('public')->exists($raw)) {
            Storage::disk('public')->delete($raw);
        }
    }

    protected static function publicUrl(string $path): string
    {
        $cfg = config('services.supabase');

        return "{$cfg['url']}/storage/v1/object/public/{$cfg['bucket']}/{$path}";
    }
}
