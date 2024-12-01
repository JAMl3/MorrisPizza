<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone');
            $table->text('delivery_address')->nullable();
            $table->enum('order_type', ['delivery', 'pickup']);
            $table->datetime('pickup_time')->nullable();
            $table->decimal('total_amount', 8, 2);
            $table->enum('status', ['pending', 'processing', 'out_for_delivery', 'completed', 'cancelled'])->default('pending');
            $table->enum('payment_method', ['cash_on_delivery', 'cash_on_pickup'])->default('cash_on_delivery');
            $table->enum('payment_status', ['pending', 'paid'])->default('pending');
            $table->text('notes')->nullable();
            $table->string('guest_token', 32)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
}; 