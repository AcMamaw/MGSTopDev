<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orderdetails', function (Blueprint $table) {
            $table->string('color')->nullable()->after('stock_id'); // varchar
            $table->string('size')->nullable()->after('color');     // varchar
        });
    }

    public function down(): void
    {
        Schema::table('orderdetails', function (Blueprint $table) {
            $table->dropColumn(['color', 'size']);
        });
    }
};
