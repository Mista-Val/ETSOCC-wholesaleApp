<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('customer_location_balances', function (Blueprint $table) {
        $table->decimal('credit_balance', 12, 2)->default(0)->after('balance');
    });
}

public function down()
{
    Schema::table('customer_location_balances', function (Blueprint $table) {
        $table->dropColumn('credit_balance');
    });
}
};
