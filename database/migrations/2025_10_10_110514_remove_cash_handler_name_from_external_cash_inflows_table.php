<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations (Remove the column).
     */
    public function up(): void
    {
        Schema::table('external_cash_inflows', function (Blueprint $table) {
            // Command to remove the specific column
            $table->dropColumn('cash_handler_name');
        });
    }

    /**
     * Reverse the migrations (Add the column back).
     */
    public function down(): void
    {
        Schema::table('external_cash_inflows', function (Blueprint $table) {
            // Command to re-add the column for rollback
            $table->string('cash_handler_name', 100)->after('source');
        });
    }
};
