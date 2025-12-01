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
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id('delivery_id'); // Primary key

            $table->foreignId('supplier_id')
                  ->constrained('suppliers', 'supplier_id')
                  ->onDelete('cascade'); // FK

            $table->foreignId('employee_id')
                  ->nullable()          
                  ->constrained('employees', 'employee_id')
                  ->onDelete('cascade'); // FK

            // New column for the employee who received the delivery
            $table->foreignId('received_by')->nullable()
                  ->constrained('employees', 'employee_id')
                  ->onDelete('set null');
           
            $table->date('delivery_date_request');
            $table->date('delivery_date_received')->nullable(); // Nullable
            $table->string('status'); // e.g., Pending, Received
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
