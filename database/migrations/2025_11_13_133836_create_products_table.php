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
        Schema::create('products', function (Blueprint $table) {
            $table->id('product_id'); // Primary key

            $table->foreignId('supplier_id')
                ->nullable()
                ->constrained('suppliers', 'supplier_id')
                ->nullOnDelete();

            $table->string('product_name');
            $table->text('description')->nullable();
            $table->string('unit');

            // Markup rule (e.g. 0.30 = 30% markup over cost)
            $table->decimal('markup_rule', 5, 2)->default(0.00);

            // Image path or URL for product picture
            $table->string('image_path')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
