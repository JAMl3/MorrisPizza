<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\MenuItem;
use App\Services\CartService;
use App\Http\Requests\CartItemRequest;
use App\Http\Requests\CheckoutRequest;
use App\Http\Resources\CartResource;
use App\DTOs\CartData;
use App\DTOs\CheckoutData;
use App\Exceptions\Cart\CartEmptyException;
use App\Exceptions\Cart\ItemNotAvailableException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index()
    {
        try {
            $cart = Cart::getCurrent();
            
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => new CartResource($cart)
                ]);
            }

            return view('cart.index', compact('cart'));
        } catch (Exception $e) {
            Log::error('Cart index error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch cart'
                ], 500);
            }
            throw $e;
        }
    }

    public function add(CartItemRequest $request)
    {
        try {
            $cartData = CartData::fromRequest($request->validated());
            
            $menuItem = MenuItem::with(['category'])->findOrFail($cartData->menuItemId);
            
            if (!$menuItem->is_available) {
                throw new ItemNotAvailableException();
            }

            $cart = $this->cartService->addItem(
                $menuItem, 
                $cartData->quantity, 
                $cartData->specialInstructions
            );

            return response()->json([
                'success' => true,
                'message' => 'Item added to cart successfully',
                'data' => new CartResource($cart)
            ]);

        } catch (Exception $e) {
            Log::error('Cart add error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($e instanceof ItemNotAvailableException) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 422);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to add item to cart'
            ], 500);
        }
    }

    public function remove(Request $request)
    {
        try {
            $validated = $request->validate([
                'cart_item_id' => 'required|exists:cart_items,id'
            ]);

            $cart = $this->cartService->removeItem($validated['cart_item_id']);
            
            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart',
                'data' => new CartResource($cart)
            ]);

        } catch (Exception $e) {
            Log::error('Cart remove error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove item from cart'
            ], 500);
        }
    }

    public function clear()
    {
        try {
            $cart = $this->cartService->clearCart();
            
            return response()->json([
                'success' => true,
                'message' => 'Cart cleared successfully',
                'data' => new CartResource($cart)
            ]);

        } catch (Exception $e) {
            Log::error('Cart clear error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cart'
            ], 500);
        }
    }

    public function checkout()
    {
        try {
            $cart = Cart::getCurrent();
            
            if ($cart->items->isEmpty()) {
                throw new CartEmptyException();
            }

            $userProfile = auth()->check() ? auth()->user()->profile : null;
            
            // Calculate totals
            $subtotal = $cart->items->sum(function ($item) {
                return $item->quantity * $item->unit_price;
            });
            
            $deliveryFee = config('app.delivery_fee', 2.50);
            $total = $subtotal + $deliveryFee;

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'cart' => new CartResource($cart),
                        'user_profile' => $userProfile,
                        'subtotal' => $subtotal,
                        'delivery_fee' => $deliveryFee,
                        'total' => $total
                    ]
                ]);
            }

            return view('cart.checkout', [
                'cart' => $cart,
                'userProfile' => $userProfile,
                'subtotal' => $subtotal,
                'deliveryFee' => $deliveryFee,
                'total' => $total
            ]);

        } catch (Exception $e) {
            Log::error('Cart checkout error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($e instanceof CartEmptyException) {
                if (request()->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $e->getMessage()
                    ], 422);
                }
                return redirect()->route('cart.index')
                    ->withErrors(['cart' => $e->getMessage()]);
            }
            
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to proceed to checkout'
                ], 500);
            }
            
            return redirect()->route('cart.index')
                ->withErrors(['error' => 'Failed to proceed to checkout']);
        }
    }

    public function processCheckout(CheckoutRequest $request)
    {
        try {
            DB::beginTransaction();

            $checkoutData = CheckoutData::fromRequest($request->validated());

            if (!Auth::check() && $checkoutData->checkoutType === 'create_account') {
                $user = User::create([
                    'name' => $checkoutData->customerName,
                    'email' => $checkoutData->customerEmail,
                    'password' => Hash::make($checkoutData->password),
                ]);

                $user->profile()->create([
                    'default_name' => $checkoutData->customerName,
                    'default_email' => $checkoutData->customerEmail,
                    'default_phone' => $checkoutData->customerPhone,
                    'default_address' => $checkoutData->deliveryAddress,
                ]);

                Auth::login($user);
            }

            $order = $this->cartService->createOrder([
                'customer_name' => $checkoutData->customerName,
                'customer_email' => $checkoutData->customerEmail,
                'customer_phone' => $checkoutData->customerPhone,
                'delivery_type' => $checkoutData->deliveryType,
                'delivery_address' => $checkoutData->deliveryAddress,
                'delivery_instructions' => $checkoutData->deliveryInstructions,
            ]);

            DB::commit();

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Order placed successfully',
                    'data' => [
                        'order_id' => $order->id,
                        'redirect_url' => route('orders.show', $order)
                    ]
                ]);
            }

            return redirect()->route('orders.show', $order)
                ->with('success', 'Order placed successfully!');

        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Checkout process error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to process checkout. Please try again.'
                ], 500);
            }

            return redirect()->route('cart.checkout')
                ->withErrors(['error' => 'Failed to process checkout. Please try again.'])
                ->withInput();
        }
    }
} 