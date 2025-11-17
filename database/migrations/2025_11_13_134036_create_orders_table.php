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
      Schema::create('orders', function (Blueprint $table) {
            $table->id('order_id'); // Primary key
            $table->foreignId('customer_id')->constrained('customers', 'customer_id')->onDelete('cascade'); // FK
            $table->date('order_date');
            $table->decimal('total_amount', 12, 2);
            $table->string('status'); // e.g., Pending, Completed, Shipped
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
