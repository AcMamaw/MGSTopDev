<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('user_id'); // PK
            $table->string('username'); 
            $table->string('password'); 
            $table->unsignedBigInteger('employee_id')->nullable(); // FK to employees
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('employee_id')
                  ->references('employee_id') // must match the PK name in employees
                  ->on('employees')
                  ->onDelete('set null'); // if employee deleted, set null
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['employee_id']);
        });

        Schema::dropIfExists('users');
    }
};
