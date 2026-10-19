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
        // Format data produk sesuai Modul 3 (Backend)
        return [
            'id' => $this->product_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'notes' => $this->notes,
            'price' => (float) $this->price,
            'stock' => (int) $this->stock,
            'rating' => (float) $this->rating,
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
