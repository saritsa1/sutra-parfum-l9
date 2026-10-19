<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    /**
     * Otorisasi request (Modul 3: Skala Produksi - Validasi Terisolasi)
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi input terisolasi untuk pembuatan produk baru
     */
    public function rules(): array
    {
        return [
            'name'        => 'required|string|max:150',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,category_id',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'notes'       => 'nullable|string|max:255',
            'color'       => 'nullable|string|max:20',
            'rating'      => 'nullable|numeric|between:0,5',
            'img'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];
    }

    /**
     * Pesan kesalahan validasi yang ramah pengguna
     */
    public function messages(): array
    {
        return [
            'name.required'        => 'Nama produk wajib diisi.',
            'name.max'             => 'Nama produk maksimal 150 karakter.',
            'category_id.required' => 'Kategori produk wajib dipilih.',
            'category_id.exists'   => 'Kategori yang dipilih tidak ditemukan dalam sistem.',
            'price.required'       => 'Harga produk wajib diisi.',
            'price.numeric'        => 'Harga produk harus berupa nilai angka.',
            'stock.required'       => 'Stok produk wajib diisi.',
            'stock.integer'        => 'Stok produk harus berupa bilangan bulat.',
            'img.image'            => 'Berkas unggahan harus berupa file gambar.',
            'img.mimes'            => 'Format gambar yang diperbolehkan adalah jpg, jpeg, png, atau webp.',
            'img.max'              => 'Ukuran berkas gambar maksimal 2MB (2048 KB).',
        ];
    }
}
