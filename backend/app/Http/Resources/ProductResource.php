<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // Rating = rata-rata ulasan kalau sudah ada ulasan, kalau belum pakai rating awal produk
        $jumlahUlasan = (int) ($this->reviews_count ?? 0);
        $rating = $jumlahUlasan > 0
            ? round((float) $this->reviews_avg_rating, 2)
            : (float) $this->rating;

        // Format data produk sesuai Modul 3 (Backend)
        return [
            'id' => $this->product_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'notes' => $this->notes,
            'color' => $this->color,
            'price' => (float) $this->price,
            'stock' => (int) $this->stock,
            'rating' => $rating,
            'reviews_count' => $jumlahUlasan,
            // accessor img pada model Product sudah menghasilkan URL lengkap
            'img_url' => $this->img,
            'category' => [
                'id' => (int) $this->category_id,
                'name' => $this->category?->category,
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
