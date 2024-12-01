<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For SQLite, we need to create a new table and copy data
        Schema::create('discount_codes_new', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->decimal('discount_percentage', 5, 2);
            $table->timestamp('valid_from');
            $table->timestamp('valid_until');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Copy data from old table to new table
        DB::statement('INSERT INTO discount_codes_new (id, code, discount_percentage, valid_from, valid_until, is_active, created_at, updated_at)
            SELECT id, code, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, is_active, created_at, updated_at
            FROM discount_codes');

        // Drop old table
        Schema::drop('discount_codes');

        // Rename new table to old table name
        Schema::rename('discount_codes_new', 'discount_codes');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // For the down migration, we'll recreate the original structure
        Schema::create('discount_codes_old', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('description')->nullable();
            $table->string('type')->check("type in ('fixed', 'percentage')");
            $table->decimal('value');
            $table->integer('max_uses')->nullable();
            $table->integer('times_used')->default(0);
            $table->decimal('min_order_amount')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Copy data back
        DB::statement('INSERT INTO discount_codes_old (id, code, type, value, is_active, created_at, updated_at)
            SELECT id, code, "percentage", discount_percentage, is_active, created_at, updated_at
            FROM discount_codes');

        // Drop new table
        Schema::drop('discount_codes');

        // Rename old table back
        Schema::rename('discount_codes_old', 'discount_codes');
    }
};
