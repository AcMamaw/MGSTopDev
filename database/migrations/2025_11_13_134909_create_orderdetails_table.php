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
        Schema::create('orderdetails', function (Blueprint $table) {
            $table->id('orderdetails_id'); // Primary key

            // Foreign keys
            $table->foreignId('order_id')
                ->constrained('orders', 'order_id')
                ->onDelete('cascade');

            $table->foreignId('stock_id')
                ->constrained('inventory', 'stock_id')
                ->onDelete('cascade');

            $table->integer('quantity'); // Quantity ordered
            $table->decimal('price', 12, 2); // Price per unit
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orderdetails');
    }
};
