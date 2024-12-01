<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class Cart extends Model
{
    protected $fillable = [
        'session_id',
        'user_id'
    ];

    protected $appends = ['subtotal', 'delivery_fee', 'total_amount'];

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    public function getSubtotalAttribute()
    {
        return $this->items->sum('subtotal');
    }

    public function getDeliveryFeeAttribute()
    {
        return $this->items->isEmpty() ? 0 : 2.50;
    }

    public function getTotalAmountAttribute()
    {
        return $this->subtotal + $this->delivery_fee;
    }

    public static function getCurrent()
    {
        $sessionId = Session::getId();
        $userId = auth()->id();

        $cart = static::firstOrCreate([
            'session_id' => $sessionId,
            'user_id' => $userId
        ]);

        return $cart->load('items.menuItem');
    }

    public function addItem($menuItemId, $quantity = 1, $specialInstructions = null)
    {
        $menuItem = MenuItem::findOrFail($menuItemId);
        
        $cartItem = $this->items()->where('menu_item_id', $menuItemId)->first();
        
        if ($cartItem) {
            $cartItem->update([
                'quantity' => $cartItem->quantity + $quantity,
                'special_instructions' => $specialInstructions
            ]);
        } else {
            $cartItem = $this->items()->create([
                'menu_item_id' => $menuItemId,
                'quantity' => $quantity,
                'unit_price' => $menuItem->price,
                'special_instructions' => $specialInstructions
            ]);
        }

        return $cartItem;
    }

    public function removeItem($cartItemId)
    {
        return $this->items()->where('id', $cartItemId)->delete();
    }

    public function clear()
    {
        return $this->items()->delete();
    }

    public function updateQuantity($cartItemId, $quantity)
    {
        $cartItem = $this->items()->findOrFail($cartItemId);
        
        if ($quantity <= 0) {
            return $this->removeItem($cartItemId);
        }

        return $cartItem->update(['quantity' => $quantity]);
    }
} 