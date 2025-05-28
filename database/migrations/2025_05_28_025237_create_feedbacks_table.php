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
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->id('feedback_id'); // PK
            $table->foreignId('employee_id')->constrained('employees', 'employee_id')->onDelete('cascade'); // FK to employees
            $table->timestamp('submitted_at')->useCurrent(); // When the feedback was submitted
            $table->text('content');
            $table->string('sentiment')->nullable(); // e.g., positive, negative, neutral
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedbacks');
    }
};
