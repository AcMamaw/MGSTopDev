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
        Schema::create('colors', function (Blueprint $table) {
            $table->id('color_id');          // Primary key
            $table->string('color_name');    // Name of the color
            $table->string('color_code')->nullable(); // Optional HEX code (#FFFFFF)
            $table->text('description')->nullable();  // Optional description
            $table->timestamps();            // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('colors');
    }
};
