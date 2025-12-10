<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up()
{
    Schema::table('reports', function (Blueprint $table) {
        $table->string('coverage')->nullable()->after('report_type');
    });
}

public function down()
{
    Schema::table('reports', function (Blueprint $table) {
        $table->dropColumn('coverage');
    });
}
};
