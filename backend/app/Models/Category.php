<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    // Jika primary key bukan 'id', tentukan di sini
    protected $primaryKey = 'category_id';

    // Fields yang boleh diisi massal
    protected $fillable = [
        'category',
    ];

    /**
     * Relasi One to Many ke tabel products (Bab III Modul)
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'category_id', 'category_id');
    }
}
