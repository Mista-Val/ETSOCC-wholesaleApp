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
        Schema::create('expense_logs', function (Blueprint $table) {
          $table->id(); // auto-incrementing ID
            $table->integer('location_id'); // location_id as integer
            $table->float('amount', 10, 2)->nullable(); // expense amount
            $table->string('purpose')->nullable(); // purpose of the expense
            $table->string('remarks')->nullable(); // remarks, nullable
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_logs');
    }
};
