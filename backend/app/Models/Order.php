<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    // Status yang tidak bisa diubah lagi oleh admin
    public const STATUS_FINAL = ['Selesai', 'Dibatalkan'];

    // Status yang dihitung sebagai pendapatan
    public const STATUS_PENDAPATAN = ['Dibayar', 'Diproses', 'Dikirim', 'Selesai'];

    protected $fillable = [
        'order_no', 'customer_name', 'phone', 'address',
        'shipping_name', 'shipping_est', 'shipping_cost',
        'payment_id', 'payment_name', 'payment_type', 'va_number',
        'subtotal', 'service_fee', 'payment_fee', 'total',
        'status', 'expires_at',
    ];

    protected $casts = [
        'shipping_cost' => 'float',
        'subtotal'      => 'float',
        'service_fee'   => 'float',
        'payment_fee'   => 'float',
        'total'         => 'float',
        'expires_at'    => 'datetime',
    ];

    /** Relasi One to Many: satu pesanan punya banyak item */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function isFinal(): bool
    {
        return in_array($this->status, self::STATUS_FINAL, true);
    }
}
