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
        Schema::create('stock_transfer_requests_products', function (Blueprint $table) {
           $table->id();

            $table->unsignedBigInteger('transfer_request_id'); // reference to stock_transfer_requests
            $table->unsignedBigInteger('product_id');          // reference to product

            $table->integer('set_quantity')->default(0);      // quantity to transfer
            $table->text('remarks')->nullable();             // any remarks
            $table->string('type')->nullable();              // type (in/out/transfer etc.)

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_transfer_requests_products');
    }
};
