<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\MenuItem;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderItem;
use App\Notifications\OrderConfirmation;

class CartService
{
    public function addItem(MenuItem $menuItem, int $quantity, ?string $specialInstructions = null)
    {
        try {
            $cart = $this->getCurrentCart();
            
            if (!$menuItem->exists) {
                throw new ModelNotFoundException('Menu item not found');
            }
            
            $existingItem = $cart->items()->where('menu_item_id', $menuItem->id)->first();
            
            if ($existingItem) {
                $newQuantity = $existingItem->quantity + $quantity;
                
                // Add a reasonable maximum quantity limit
                if ($newQuantity > 20) {
                    throw new Exception('Maximum quantity limit exceeded');
                }
                
                $existingItem->update([
                    'quantity' => $newQuantity,
                    'special_instructions' => $specialInstructions ?? $existingItem->special_instructions
                ]);
            } else {
                if ($quantity > 20) {
                    throw new Exception('Maximum quantity limit exceeded');
                }
                
                $cart->items()->create([
                    'menu_item_id' => $menuItem->id,
                    'quantity' => $quantity,
                    'special_instructions' => $specialInstructions,
                    'unit_price' => $menuItem->price
                ]);
            }

            $this->recalculateCart($cart);
            return $cart->fresh(['items.menuItem']);

        } catch (Exception $e) {
            Log::error('Error adding item to cart: ' . $e->getMessage(), [
                'menu_item_id' => $menuItem->id,
                'quantity' => $quantity,
                'exception' => $e
            ]);
            throw $e;
        }
    }

    public function removeItem($cartItemId)
    {
        try {
            $cart = $this->getCurrentCart();
            
            $item = $cart->items()->find($cartItemId);
            if (!$item) {
                throw new ModelNotFoundException('Cart item not found');
            }
            
            $item->delete();
            $this->recalculateCart($cart);
            
            return $cart->fresh(['items.menuItem']);

        } catch (Exception $e) {
            Log::error('Error removing item from cart: ' . $e->getMessage(), [
                'cart_item_id' => $cartItemId,
                'exception' => $e
            ]);
            throw $e;
        }
    }

    public function clearCart()
    {
        try {
            $cart = $this->getCurrentCart();
            $cart->items()->delete();
            $this->recalculateCart($cart);
            
            return $cart->fresh();

        } catch (Exception $e) {
            Log::error('Error clearing cart: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            throw $e;
        }
    }

    protected function getCurrentCart()
    {
        try {
            return Cart::getCurrent();
        } catch (Exception $e) {
            Log::error('Error getting current cart: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            throw $e;
        }
    }

    protected function recalculateCart(Cart $cart)
    {
        try {
            $subtotal = $cart->items->sum(function ($item) {
                return $item->unit_price * $item->quantity;
            });

            $cart->update([
                'subtotal' => $subtotal,
                'delivery_fee' => $cart->delivery_fee ?? 0,
                'total_amount' => $subtotal + ($cart->delivery_fee ?? 0)
            ]);

            return $cart;

        } catch (Exception $e) {
            Log::error('Error recalculating cart: ' . $e->getMessage(), [
                'cart_id' => $cart->id,
                'exception' => $e
            ]);
            throw $e;
        }
    }

    public function createOrder($data)
    {
        try {
            DB::beginTransaction();

            // Create the order
            $order = Order::create([
                'user_id' => $data['user_id'] ?? null,
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'],
                'customer_phone' => $data['customer_phone'],
                'delivery_address' => $data['delivery_address'],
                'delivery_instructions' => $data['delivery_instructions'] ?? null,
                'delivery_type' => $data['delivery_type'],
                'order_type' => $data['delivery_type'] === 'delivery' ? 'delivery' : 'pickup',
                'status' => 'pending',
                'total_amount' => $this->calculateTotal(),
            ]);

            // Create order items
            foreach ($this->getItems() as $item) {
                $order->items()->create([
                    'menu_item_id' => $item['menu_item_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'subtotal' => $item['quantity'] * $item['price']
                ]);
            }

            // Load the items relationship for the notification
            $order->load('items.menuItem');

            // Send order confirmation
            try {
                $order->notify(new OrderConfirmation($order));
            } catch (\Exception $e) {
                Log::error('Failed to send order confirmation: ' . $e->getMessage(), [
                    'order_id' => $order->id,
                    'customer_email' => $order->customer_email,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                // Continue execution even if notification fails
            }

            DB::commit();

            // Clear the cart after successful order
            $this->clear();

            return $order;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order creation failed: ' . $e->getMessage(), [
                'data' => $data,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
} 