<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DiscountCode;

class DiscountCodeSeeder extends Seeder
{
    public function run()
    {
        DiscountCode::create([
            'code' => 'MORRIS',
            'description' => 'Test discount code',
            'type' => 'percentage',
            'value' => 10.00,
            'max_uses' => null,
            'times_used' => 0,
            'min_order_amount' => null,
            'starts_at' => null,
            'expires_at' => null,
            'is_active' => true
        ]);
    }
} 