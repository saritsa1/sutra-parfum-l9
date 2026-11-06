<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Harga tidak diterima dari browser: yang dikirim hanya id produk dan jumlah.
     * Harga, ongkir, dan biaya dihitung ulang oleh server di OrderController.
     */
    public function rules(): array
    {
        return [
            'customer_name'      => 'required|string|min:3|max:100',
            'phone'              => ['required', 'regex:/^08\d{8,12}$/'],
            'address'            => 'required|string|min:10|max:500',
            'shipping_id'        => 'required|string',
            'payment_id'         => 'required|string',
            'items'              => 'required|array|min:1|max:20',
            'items.*.product_id' => 'required|integer|distinct|exists:products,product_id',
            'items.*.qty'        => 'required|integer|min:1|max:20',
        ];
    }

    public function messages(): array
    {
        return [
            'customer_name.required' => 'Nama penerima wajib diisi.',
            'customer_name.min'      => 'Nama minimal 3 karakter.',
            'phone.required'         => 'Nomor HP wajib diisi.',
            'phone.regex'            => 'Nomor HP harus diawali 08 dan 10-14 digit.',
            'address.required'       => 'Alamat wajib diisi.',
            'address.min'            => 'Alamat minimal 10 karakter.',
            'shipping_id.required'   => 'Pilih opsi pengiriman.',
            'payment_id.required'    => 'Pilih metode pembayaran.',
            'items.required'         => 'Keranjang tidak boleh kosong.',
            'items.min'              => 'Keranjang tidak boleh kosong.',
            'items.*.product_id.exists' => 'Ada produk di keranjang yang sudah tidak tersedia.',
            'items.*.product_id.distinct' => 'Produk yang sama dikirim dua kali.',
            'items.*.qty.min'        => 'Jumlah minimal 1.',
            'items.*.qty.max'        => 'Jumlah maksimal 20 per produk.',
        ];
    }
}
