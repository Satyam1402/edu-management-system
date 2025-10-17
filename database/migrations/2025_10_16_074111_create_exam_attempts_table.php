<?php
// database/migrations/2025_10_16_xxxxxx_create_exam_attempts_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->integer('score')->nullable();
            $table->integer('total_marks')->default(100);
            $table->enum('result', ['pass', 'fail', 'absent'])->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->json('answers')->nullable(); // Store exam answers as JSON
            $table->timestamps();

            // Ensure student can only attempt exam once
            $table->unique(['exam_id', 'student_id']);

            // Indexes
            $table->index(['student_id', 'result']);
            $table->index(['exam_id', 'result']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_attempts');
    }
};
