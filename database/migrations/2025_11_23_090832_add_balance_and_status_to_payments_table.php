<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Add balance and status after 'cash' column
            $table->decimal('balance', 10, 2)->default(0)->after('cash');
            $table->string('status')->default('Not Paid')->after('balance');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['balance', 'status']);
        });
    }
};
