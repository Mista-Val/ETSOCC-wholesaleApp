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
        Schema::table('stock_transfer_requests_products', function (Blueprint $table) {
        $table->integer('received_quantity')->nullable()->after('set_quantity');        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_transfer_requests_products', function (Blueprint $table) {
            //
        });
    }
};
