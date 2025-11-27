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
        Schema::create('external_cash_inflows', function (Blueprint $table) {
            $table->id();
            // Fields matching the form inputs and labels:
            $table->string('source', 100);
            $table->string('cash_handler_name', 100);
            $table->decimal('amount', 10, 2);
            $table->date('received_date');
            $table->string('received_from', 100);
            $table->string('remarks', 500)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('external_cash_inflows');
    }
};
