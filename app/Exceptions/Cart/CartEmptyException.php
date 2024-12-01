<?php

namespace App\Exceptions\Cart;

use Exception;

class CartEmptyException extends Exception
{
    public function __construct($message = 'Your cart is empty')
    {
        parent::__construct($message);
    }

    public function render($request)
    {
        if ($request->wantsJson()) {
            return response()->json([
                'error' => true,
                'message' => $this->getMessage()
            ], 422);
        }

        return redirect()->route('cart.index')
            ->withErrors(['cart' => $this->getMessage()]);
    }
} 