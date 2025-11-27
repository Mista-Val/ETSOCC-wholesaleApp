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
        Schema::create('locations', function (Blueprint $table) {
             $table->id(); // id
            $table->string('name')->nullable(); // location name
            $table->string('type')->nullable(); // type of location
            $table->string('status')->nullable();
            $table->unsignedBigInteger('user_id'); // manager id
            $table->text('description')->nullable(); // description
            $table->text('address')->nullable(); // description
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
