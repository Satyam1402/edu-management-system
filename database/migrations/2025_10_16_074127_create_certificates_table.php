<?php
// database/migrations/2025_10_16_xxxxxx_create_certificates_table.php

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
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('number')->unique();
            $table->enum('status', ['requested', 'approved', 'issued'])->default('requested');
            $table->timestamp('issued_at')->nullable();
            $table->timestamps();

            // Indexes for better performance
            $table->index(['student_id', 'status']);
            $table->index(['course_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
