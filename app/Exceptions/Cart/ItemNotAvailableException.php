<?php

namespace App\Exceptions\Cart;

use Exception;

class ItemNotAvailableException extends Exception
{
    public function __construct($message = 'This item is currently unavailable')
    {
        parent::__construct($message);
    }

    public function render($request)
    {
        return response()->json([
            'error' => true,
            'message' => $this->getMessage()
        ], 422);
    }
} 