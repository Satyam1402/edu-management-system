<?php
// database/migrations/2025_10_16_xxxxxx_create_exams_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('mode', ['offline', 'paper'])->default('offline');
            $table->date('exam_date')->nullable();
            $table->time('start_time')->nullable();
            $table->integer('duration_minutes')->default(180);
            $table->integer('total_marks')->default(100);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            // Indexes
            $table->index(['course_id', 'status']);
            $table->index('exam_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
