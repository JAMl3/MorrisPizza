<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DiscountCode;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DiscountCodeController extends Controller
{
    public function validateDiscountCode(Request $request)
    {
        try {
            $code = $request->input('code');
            
            $discountCode = DiscountCode::where('code', $code)
                ->where('is_active', true)
                ->where('valid_from', '<=', now())
                ->where('valid_until', '>=', now())
                ->first();

            if (!$discountCode) {
                return response()->json([
                    'valid' => false,
                    'message' => 'Invalid discount code'
                ], Response::HTTP_BAD_REQUEST);
            }

            return response()->json([
                'valid' => true,
                'discount_percentage' => $discountCode->discount_percentage
            ]);

        } catch (\Exception $e) {
            \Log::error('Discount code validation error: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred while validating the discount code'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
} 