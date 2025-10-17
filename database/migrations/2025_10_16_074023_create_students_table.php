<?php
// database/migrations/2025_10_16_xxxxxx_create_students_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('franchise_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('email')->unique()->nullable();
            $table->string('phone', 20)->nullable();
            $table->enum('status', ['enquiry', 'admission', 'active', 'inactive'])->default('enquiry');
            $table->timestamps();

            // Indexes for better query performance
            $table->index(['franchise_id', 'status']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
