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
        Schema::create('cash_remittance', function (Blueprint $table) {
           $table->id(); // Primary key
            $table->BigInteger('receiver_id');
            $table->BigInteger('location_id');
            $table->float('amount');
            $table->string('status')->nullable(); // Modify enum as needed
            $table->string('remarks')->nullable();
           $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_remittance');
    }
};
