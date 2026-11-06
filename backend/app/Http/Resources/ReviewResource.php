<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'         => $this->id,
            'product_id' => $this->product_id,
            'product'    => $this->whenLoaded('product', fn () => $this->product?->name),
            'name'       => $this->name,
            'rating'     => (int) $this->rating,
            'comment'    => $this->comment,
            'created_at' => $this->created_at,
        ];
    }
}
