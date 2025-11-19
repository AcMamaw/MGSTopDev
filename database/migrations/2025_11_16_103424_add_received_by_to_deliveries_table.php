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
        Schema::table('deliveries', function (Blueprint $table) {
            $table->foreignId('received_by')->nullable()
                  ->constrained('employees', 'employee_id')
                  ->onDelete('set null')
                  ->after('employee_id'); // place after employee_id column
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->dropForeign(['received_by']);
            $table->dropColumn('received_by');
        });
    }
};
