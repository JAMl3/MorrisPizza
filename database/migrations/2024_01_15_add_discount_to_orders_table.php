<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('discount_code')->nullable()->after('total_amount');
            $table->decimal('discount_amount', 8, 2)->nullable()->after('discount_code');
            $table->decimal('subtotal', 8, 2)->nullable()->after('total_amount');
        });

        // Update existing orders to set subtotal equal to total_amount
        DB::table('orders')->whereNull('subtotal')->update([
            'subtotal' => DB::raw('total_amount')
        ]);
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['discount_code', 'discount_amount', 'subtotal']);
        });
    }
}; 