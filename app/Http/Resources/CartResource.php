<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'cartItems' => $this->items->map(function($item) {
                return [
                    'id' => $item->id,
                    'menu_item' => $item->menuItem,
                    'quantity' => $item->quantity,
                    'special_instructions' => $item->special_instructions,
                    'subtotal' => $item->subtotal
                ];
            }),
            'subtotal' => $this->subtotal,
            'delivery_fee' => $this->delivery_fee,
            'total' => $this->total_amount
        ];
    }
} 