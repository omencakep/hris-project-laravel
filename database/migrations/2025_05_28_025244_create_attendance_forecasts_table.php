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
        Schema::create('attendance_forecasts', function (Blueprint $table) {
            $table->id('forecast_id'); // PK
            $table->foreignId('employee_id')->constrained('employees', 'employee_id')->onDelete('cascade'); // FK to employees
            $table->date('forecast_date');
            $table->string('predicted_status'); // e.g., present, absent, leave
            $table->float('confidence_score')->nullable(); // Confidence of the prediction
            $table->timestamps();

            $table->unique(['employee_id', 'forecast_date']); // Only one forecast per employee per day
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_forecasts');
    }
};
