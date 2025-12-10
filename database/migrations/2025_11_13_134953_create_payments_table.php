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
        Schema::create('payments', function (Blueprint $table) {
            $table->id('payment_id'); // Primary key

            // Foreign keys
            $table->foreignId('order_id')
                ->constrained('orders', 'order_id')
                ->onDelete('cascade');

            $table->foreignId('employee_id')
                ->constrained('employees', 'employee_id')
                ->onDelete('cascade');

            $table->date('payment_date'); 
            $table->decimal('amount', 12, 2); 
            $table->decimal('profit', 12, 2)->default(0); 
            $table->decimal('cash', 12, 2);
            $table->decimal('change_amount', 12, 2); 
            $table->string('payment_method', 50);
            $table->string('reference_number', 100)->nullable(); 
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
