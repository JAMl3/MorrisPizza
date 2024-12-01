<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            // Remove old address column if it exists
            if (Schema::hasColumn('user_profiles', 'default_address')) {
                $table->dropColumn('default_address');
            }
            
            // Add new address fields
            $table->string('default_address_line1')->nullable();
            $table->string('default_address_line2')->nullable();
            $table->string('default_city')->nullable();
            $table->string('default_postcode')->nullable();
        });
    }

    public function down()
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'default_address_line1',
                'default_address_line2',
                'default_city',
                'default_postcode'
            ]);
            
            // Restore old address column
            $table->string('default_address')->nullable();
        });
    }
}; 