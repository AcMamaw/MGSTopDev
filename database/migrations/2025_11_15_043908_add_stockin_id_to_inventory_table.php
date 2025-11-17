<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory', function (Blueprint $table) {
            $table->unsignedBigInteger('stockin_id')->after('stock_id');
            $table->foreign('stockin_id')
                  ->references('stockin_id')
                  ->on('stockins')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('inventory', function (Blueprint $table) {
            $table->dropForeign(['stockin_id']);
            $table->dropColumn('stockin_id');
        });
    }
};
