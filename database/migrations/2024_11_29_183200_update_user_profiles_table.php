<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            // First, check if the old columns exist and drop them
            if (Schema::hasColumn('user_profiles', 'default_address')) {
                $table->dropColumn('default_address');
            }
            
            // Add new columns if they don't exist
            if (!Schema::hasColumn('user_profiles', 'default_address_line1')) {
                $table->string('default_address_line1')->nullable();
            }
            if (!Schema::hasColumn('user_profiles', 'default_address_line2')) {
                $table->string('default_address_line2')->nullable();
            }
            if (!Schema::hasColumn('user_profiles', 'default_city')) {
                $table->string('default_city')->nullable();
            }
            if (!Schema::hasColumn('user_profiles', 'default_postcode')) {
                $table->string('default_postcode')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            // Drop new columns
            $table->dropColumn([
                'default_address_line1',
                'default_address_line2',
                'default_city',
                'default_postcode'
            ]);
            
            // Restore old column
            $table->string('default_address')->nullable();
        });
    }
}; 