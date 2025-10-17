<?php
// database/migrations/2025_10_17_062326_create_exam_results_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('exam_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade'); // Reference existing exams table
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->integer('marks_obtained');
            $table->decimal('percentage', 5, 2);
            $table->enum('result', ['pass', 'fail']);
            $table->dateTime('exam_start_time')->nullable();
            $table->dateTime('exam_end_time')->nullable();
            $table->json('answers')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->unique(['exam_id', 'student_id']); // Prevent duplicate attempts
        });
    }

    public function down()
    {
        Schema::dropIfExists('exam_results');
    }
};
