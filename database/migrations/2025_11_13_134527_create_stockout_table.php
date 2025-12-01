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
        Schema::create('stockout', function (Blueprint $table) {
            $table->id('stockout_id'); // Primary key

            // Foreign keys
            $table->foreignId('stock_id')
                ->constrained('inventory', 'stock_id')
                ->onDelete('cascade');

            $table->foreignId('employee_id')
                ->constrained('employees', 'employee_id')
                ->onDelete('cascade');

            $table->string('size')->nullable();
            $table->string('product_type')->nullable();
            $table->integer('quantity_out'); // Quantity taken out
            $table->date('date_out'); // Date of stock out
            $table->text('reason'); // Reason for stock out
            $table->string('status'); // Status (Pending, Approved, etc.)

            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('employees', 'employee_id')
                ->onDelete('set null'); // Nullable FK

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stockout');
    }
};
