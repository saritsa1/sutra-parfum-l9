<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'order_no'      => $this->order_no,
            'customer_name' => $this->customer_name,
            'phone'         => $this->phone,
            'address'       => $this->address,
            'shipping' => [
                'name' => $this->shipping_name,
                'est'  => $this->shipping_est,
                'cost' => (float) $this->shipping_cost,
            ],
            'payment' => [
                'id'        => $this->payment_id,
                'name'      => $this->payment_name,
                'type'      => $this->payment_type,
                'va_number' => $this->va_number,
            ],
            'subtotal'    => (float) $this->subtotal,
            'service_fee' => (float) $this->service_fee,
            'payment_fee' => (float) $this->payment_fee,
            'total'       => (float) $this->total,
            'status'      => $this->status,
            'expires_at'  => $this->expires_at,
            'created_at'  => $this->created_at,
            'items'       => $this->whenLoaded('items', fn () => $this->items->map(fn ($i) => [
                'id'         => $i->id,
                'product_id' => $i->product_id,
                'name'       => $i->name,
                'price'      => (float) $i->price,
                'qty'        => (int) $i->qty,
                'subtotal'   => (float) $i->price * (int) $i->qty,
                'img_url'    => $i->img,
                'color'      => $i->color,
            ])->values()),
        ];
    }
}
