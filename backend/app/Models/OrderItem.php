<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'product_id', 'name', 'price', 'qty', 'img', 'color'];

    protected $casts = [
        'price' => 'float',
        'qty'   => 'integer',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /** product_id bisa NULL kalau produknya sudah dihapus */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}
