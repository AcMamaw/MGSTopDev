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
        Schema::create('stockins', function (Blueprint $table) {
            $table->id('stockin_id'); // Primary key
            $table->foreignId('employee_id')->constrained('employees', 'employee_id')->onDelete('cascade'); // FK to employees
            $table->foreignId('product_id')->constrained('products', 'product_id')->onDelete('cascade'); // FK to products
            $table->integer('quantity_product'); // Quantity of product
            $table->decimal('unit_cost', 12, 2); // Unit cost
            $table->decimal('total', 12, 2); // Total amount
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stockins');
    }
};
