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
        Schema::create('joborders', function (Blueprint $table) {
            $table->id('joborder_id'); // Primary Key

            // Correct FK to orderdetails
            $table->unsignedBigInteger('orderdetails_id'); // must match PK type
            $table->foreign('orderdetails_id')
                ->references('orderdetails_id')
                ->on('orderdetails')
                ->onDelete('cascade');

            $table->dateTime('joborder_created');
            $table->dateTime('joborder_end')->nullable();
            $table->integer('estimated_time'); // in minutes or hours
            $table->string('status', 50);

            // FK to employees
            $table->unsignedBigInteger('made_by'); // must match PK type
            $table->foreign('made_by')
                ->references('employee_id')
                ->on('employees')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('joborders');
    }
};
