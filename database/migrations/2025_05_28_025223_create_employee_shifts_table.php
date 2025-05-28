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
        Schema::create('employee_shifts', function (Blueprint $table) {
            // Composite Primary Key (Optional, bisa juga id biasa jika ingin)
            $table->id('employee_shift_id'); // PK
            $table->foreignId('employee_id')->constrained('employees', 'employee_id')->onDelete('cascade'); // FK to employees
            $table->foreignId('shift_id')->constrained('shifts', 'shift_id')->onDelete('cascade'); // FK to shifts
            $table->date('assigned_date'); // Tanggal penugasan shift
            $table->timestamps();

            // Unique constraint to prevent duplicate assignments for a given employee on a specific shift on a specific date
            $table->unique(['employee_id', 'shift_id', 'assigned_date'], 'employee_shift_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_shifts');
    }
};
