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
        Schema::create('employees', function (Blueprint $table) {
            $table->id('employee_id'); // Primary key
            $table->foreignId('role_id')->constrained('roles', 'role_id')->onDelete('cascade'); // FK to roles
            $table->string('fname');
            $table->string('lname');
            $table->enum('gender', ['Male', 'Female', 'Other']);
            $table->date('bdate');
            $table->string('email')->nullable()->unique();
            $table->string('contact_no');
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->string('pictures')->nullable(); // optional
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
