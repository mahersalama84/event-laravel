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
        Schema::create('wishes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->index('occasion_id');
            $table->foreignUuid('occasion_id')->references('id')->on('occasions')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable(); 
            $table->string('image', 150);           
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wishes');
    }
};
