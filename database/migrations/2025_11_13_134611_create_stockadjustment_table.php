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
       Schema::create('stockadjustment', function (Blueprint $table) {
            $table->id('stockadjustment_id'); // Primary key

            // Foreign keys
            $table->foreignId('stock_id')
                ->constrained('inventory', 'stock_id')
                ->onDelete('cascade');

            $table->foreignId('employee_id')
                ->constrained('employees', 'employee_id')
                ->onDelete('cascade');
            
            $table->string('adjustment_type'); // Type (Addition, Deduction, etc.)
            $table->integer('quantity_adjusted'); // Quantity adjusted
            $table->date('request_date'); // Date of adjustment request
            $table->text('reason'); // Reason for adjustment
            $table->string('status'); // Status (Pending, Approved, Rejected)

            // Adjusted by employee
            $table->foreignId('adjusted_by')
                ->nullable()
                ->constrained('employees', 'employee_id')
                ->onDelete('set null');

            // Approved by employee
            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('employees', 'employee_id')
                ->onDelete('set null'); 

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stockadjustment');
    }
};
