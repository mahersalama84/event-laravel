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
        Schema::create('customer_occasion', function (Blueprint $table) {
            $table->index('customer_id');
            $table->foreignUuid('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->index('occasion_id');
            $table->foreignUuid('occasion_id')->references('id')->on('occasions')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_occasion');
    }
};
