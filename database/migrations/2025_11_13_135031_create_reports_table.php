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

            // Foreign keys
            $table->foreignId('payment_id')
                ->constrained('payments', 'payment_id')
                ->onDelete('cascade');

            $table->foreignId('inventory_id')
                ->constrained('inventory', 'stock_id')
                ->onDelete('cascade');

            // New FK for Employee who generated the report
            $table->foreignId('generated_by')
                ->constrained('employees', 'employee_id')
                ->onDelete('cascade');

            $table->string('category', 100); // e.g., Stock, Sales, Delivery
            $table->string('report_type', 100); // e.g., Daily, Monthly, Yearly
            $table->date('date_created'); // Date the report is created

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
