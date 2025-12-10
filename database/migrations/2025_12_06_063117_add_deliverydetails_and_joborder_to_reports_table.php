<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            // Delivery Details - table name is 'delivery_details' (WITH underscore!)
            $table->unsignedBigInteger('deliverydetails_id')->nullable()->after('delivery_id');
            $table->foreign('deliverydetails_id')
                ->references('deliverydetails_id')
                ->on('delivery_details')  // Changed from 'deliverydetails' to 'delivery_details'
                ->onDelete('cascade');

            // Job Orders - table name is 'failed_jobs' based on screenshot
            // Wait, check if you have 'joborders' table or 'failed_jobs'?
            $table->unsignedBigInteger('joborder_id')->nullable()->after('deliverydetails_id');
            $table->foreign('joborder_id')
                ->references('joborder_id')
                ->on('joborders')  // Make sure this table exists!
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropForeign(['joborder_id']);
            $table->dropForeign(['deliverydetails_id']);
            $table->dropColumn(['joborder_id', 'deliverydetails_id']);
        });
    }
};