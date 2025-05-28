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
        Schema::create('overtime_logs', function (Blueprint $table) {
            $table->id('overtime_id'); // PK
            $table->foreignId('employee_id')->constrained('employees', 'employee_id')->onDelete('cascade'); // FK to employees
            $table->date('date');
            $table->decimal('hours', 5, 2); // e.g., 8.50 hours
            $table->text('reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('overtime_logs');
    }
};
