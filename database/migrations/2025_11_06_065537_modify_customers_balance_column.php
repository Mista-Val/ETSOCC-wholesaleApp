<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            // Change balance to DECIMAL with larger precision
            // DECIMAL(15, 2) allows up to 13 digits before decimal and 2 after
            // Max value: 9,999,999,999,999.99
            $table->decimal('balance', 15, 2)->default(0)->change();
        });
    }

    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            // Revert back to original (adjust if your original was different)
            $table->decimal('balance', 10, 2)->default(0)->change();
        });
    }
};