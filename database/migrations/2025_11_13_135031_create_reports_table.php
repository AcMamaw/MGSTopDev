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
        Schema::create('reports', function (Blueprint $table) {
            $table->id('report_id'); // Primary key

            // Payments
            $table->unsignedBigInteger('payment_id');
            $table->foreign('payment_id')
                ->references('payment_id')
                ->on('payments')
                ->onDelete('cascade');

            // Stock (inventory table, PK = stock_id)
            $table->unsignedBigInteger('stock_id');
            $table->foreign('stock_id')
                ->references('stock_id')
                ->on('inventory')
                ->onDelete('cascade');

            // Orders
            $table->unsignedBigInteger('order_id')->nullable();
            $table->foreign('order_id')
                ->references('order_id')
                ->on('orders')
                ->onDelete('cascade');

            // Order Details
            $table->unsignedBigInteger('orderdetails_id')->nullable();
            $table->foreign('orderdetails_id')
                ->references('orderdetails_id')
                ->on('orderdetails')
                ->onDelete('cascade');

            // Delivery
            $table->unsignedBigInteger('delivery_id')->nullable();
            $table->foreign('delivery_id')
                ->references('delivery_id')
                ->on('delivery_details')
                ->onDelete('cascade');

            // Employee who generated the report
            $table->unsignedBigInteger('generated_by');
            $table->foreign('generated_by')
                ->references('employee_id')
                ->on('employees')
                ->onDelete('cascade');

            // Report data
            $table->string('category', 100);    // e.g. Stock, Sales, Delivery
            $table->string('report_type', 100); // e.g. Daily, Monthly, Yearly
            $table->date('date_created');       // Date the report is created

            $table->timestamps(); // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};