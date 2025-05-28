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
        Schema::create('workforce_recommendations', function (Blueprint $table) {
            $table->id('recommendation_id'); // PK
            $table->date('date');
            $table->foreignId('department_id')->nullable()->constrained('departments', 'department_id')->onDelete('set null'); // FK to departments
            $table->integer('recommended_additional_employees')->nullable(); // Number of employees recommended
            $table->text('reasoning')->nullable(); // Explanation for the recommendation
            $table->foreignId('recommended_by')->nullable()->constrained('employees', 'employee_id')->onDelete('set null'); // FK to employees (who made the recommendation)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workforce_recommendations');
    }
};
