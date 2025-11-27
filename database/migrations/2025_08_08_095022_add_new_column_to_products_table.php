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
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('min_price', 10, 2)->after('status');
            $table->decimal('max_price', 10, 2)->after('min_price');
            $table->string('category')->after('max_price');
            $table->string('unit')->after('category');
            $table->string('destination')->after('unit');
            $table->string('remarks')->nullable()->after('destination');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
};
