<?php
// database/migrations/2025_10_17_062645_add_additional_fields_to_courses_table.php - COMPLETE VERSION
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            // Basic course details
            $table->decimal('fee', 10, 2)->nullable()->after('description');
            $table->integer('duration_months')->nullable()->after('fee');
            $table->longText('curriculum')->nullable()->after('duration_months');
            $table->text('prerequisites')->nullable()->after('curriculum');
            $table->enum('level', ['beginner', 'intermediate', 'advanced'])->default('beginner')->after('prerequisites');
            $table->json('learning_outcomes')->nullable()->after('level');
            $table->string('certificate_template')->nullable()->after('learning_outcomes');

            // Additional course management fields
            $table->string('category', 100)->nullable()->after('certificate_template');
            $table->integer('max_students')->nullable()->after('category');
            $table->decimal('passing_percentage', 5, 2)->default(60.00)->after('max_students');

            // Instructor information
            $table->string('instructor_name')->nullable()->after('passing_percentage');
            $table->string('instructor_email')->nullable()->after('instructor_name');

            // Media and marketing
            $table->string('course_image')->nullable()->after('instructor_email');
            $table->json('tags')->nullable()->after('course_image');
            $table->boolean('is_featured')->default(false)->after('tags');

            // Soft deletes for data integrity
            $table->softDeletes();
        });

        // Update existing status enum to include more options
        DB::statement("ALTER TABLE courses MODIFY COLUMN status ENUM('active', 'inactive', 'draft', 'archived') DEFAULT 'active'");
    }

    public function down()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn([
                'fee', 'duration_months', 'curriculum', 'prerequisites',
                'level', 'learning_outcomes', 'certificate_template',
                'category', 'max_students', 'passing_percentage',
                'instructor_name', 'instructor_email', 'course_image',
                'tags', 'is_featured'
            ]);
            $table->dropSoftDeletes();
        });

        // Revert status enum to original
        DB::statement("ALTER TABLE courses MODIFY COLUMN status ENUM('active', 'inactive') DEFAULT 'active'");
    }
};
