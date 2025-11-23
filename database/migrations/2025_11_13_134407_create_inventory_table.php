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
      Schema::create('inventory', function (Blueprint $table) {
            $table->id('stock_id'); // Primary key

            $table->foreignId('deliverydetails_id')
                ->nullable()          
                ->constrained('delivery_details', 'deliverydetails_id')
                ->onDelete('cascade');


            $table->foreignId('product_id')
                ->constrained('products', 'product_id')
                ->onDelete('cascade');

            $table->integer('total_stock'); // Total stock received
            $table->integer('current_stock'); // Current stock remaining
            $table->decimal('unit_cost', 12, 2); // Cost per unit
            $table->date('date_received'); // Date received

            $table->foreignId('received_by')
                ->constrained('employees', 'employee_id')
                ->onDelete('cascade');

            $table->timestamp('last_updated')->useCurrent()->useCurrentOnUpdate(); // Timestamp for last update
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory');
    }
};
