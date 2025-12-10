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
        // First, drop the existing foreign keys
        Schema::table('reports', function (Blueprint $table) {
            $table->dropForeign(['payment_id']);
            $table->dropForeign(['stock_id']);
        });

        // Then modify the columns to be nullable
        Schema::table('reports', function (Blueprint $table) {
            $table->unsignedBigInteger('payment_id')->nullable()->change();
            $table->unsignedBigInteger('stock_id')->nullable()->change();
        });

        // Re-add the foreign keys
        Schema::table('reports', function (Blueprint $table) {
            $table->foreign('payment_id')
                ->references('payment_id')
                ->on('payments')
                ->onDelete('cascade');

            $table->foreign('stock_id')
                ->references('stock_id')
                ->on('inventory')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropForeign(['payment_id']);
            $table->dropForeign(['stock_id']);
        });

        Schema::table('reports', function (Blueprint $table) {
            $table->unsignedBigInteger('payment_id')->nullable(false)->change();
            $table->unsignedBigInteger('stock_id')->nullable(false)->change();
        });

        Schema::table('reports', function (Blueprint $table) {
            $table->foreign('payment_id')
                ->references('payment_id')
                ->on('payments')
                ->onDelete('cascade');

            $table->foreign('stock_id')
                ->references('stock_id')
                ->on('inventory')
                ->onDelete('cascade');
        });
    }
};