<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('franchise_id')->constrained()->onDelete('cascade');
            $table->timestamp('enrollment_date');
            $table->enum('payment_method', ['cash', 'card', 'bank_transfer', 'upi'])->nullable();
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->enum('payment_status', ['paid', 'pending', 'partial'])->default('pending');
            $table->enum('status', ['active', 'completed', 'cancelled', 'on_hold'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamp('completion_date')->nullable();
            $table->string('grade')->nullable();
            $table->boolean('certificate_issued')->default(false);
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['student_id', 'course_id']);
            $table->index(['franchise_id', 'status']);
            $table->index('enrollment_date');
        });
    }

    public function down()
    {
        Schema::dropIfExists('enrollments');
    }
};
