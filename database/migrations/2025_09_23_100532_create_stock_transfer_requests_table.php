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
        Schema::create('stock_transfer_requests', function (Blueprint $table) {
             $table->id(); // id

            $table->string('supplier_name')->nullable(); // supplier name
            $table->BigInteger('supplier_id')->nullable(); // supplier id
            $table->unsignedBigInteger('receiver_id'); // receiver id

            $table->string('status')->nullable(); // status
            $table->string('type')->nullable(); // type of transfer

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_transfer_requests');
    }
};
