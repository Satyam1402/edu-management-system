<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            // Basic course details
            if (!Schema::hasColumn('courses', 'fee')) {
                $table->decimal('fee', 10, 2)->nullable()->after('description');
            }

            if (!Schema::hasColumn('courses', 'duration_months')) {
                $table->integer('duration_months')->nullable()->after('fee');
            }

            if (!Schema::hasColumn('courses', 'curriculum')) {
                $table->longText('curriculum')->nullable()->after('duration_months');
            }

            if (!Schema::hasColumn('courses', 'prerequisites')) {
                $table->text('prerequisites')->nullable()->after('curriculum');
            }

            if (!Schema::hasColumn('courses', 'level')) {
                $table->enum('level', ['beginner', 'intermediate', 'advanced'])->default('beginner')->after('prerequisites');
            }

            if (!Schema::hasColumn('courses', 'learning_outcomes')) {
                $table->json('learning_outcomes')->nullable()->after('level');
            }

            if (!Schema::hasColumn('courses', 'certificate_template')) {
                $table->string('certificate_template')->nullable()->after('learning_outcomes');
            }

            // Additional course management fields
            if (!Schema::hasColumn('courses', 'category')) {
                $table->string('category', 100)->nullable()->after('certificate_template');
            }

            if (!Schema::hasColumn('courses', 'max_students')) {
                $table->integer('max_students')->nullable()->after('category');
            }

            if (!Schema::hasColumn('courses', 'passing_percentage')) {
                $table->decimal('passing_percentage', 5, 2)->default(60.00)->after('max_students');
            }

            // Instructor information
            if (!Schema::hasColumn('courses', 'instructor_name')) {
                $table->string('instructor_name')->nullable()->after('passing_percentage');
            }

            if (!Schema::hasColumn('courses', 'instructor_email')) {
                $table->string('instructor_email')->nullable()->after('instructor_name');
            }

            // Media and marketing
            if (!Schema::hasColumn('courses', 'course_image')) {
                $table->string('course_image')->nullable()->after('instructor_email');
            }

            if (!Schema::hasColumn('courses', 'tags')) {
                $table->json('tags')->nullable()->after('course_image');
            }

            if (!Schema::hasColumn('courses', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('tags');
            }

            // Soft deletes
            if (!Schema::hasColumn('courses', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        // Safely update enum column
        try {
            DB::statement("ALTER TABLE courses MODIFY COLUMN status ENUM('active', 'inactive', 'draft', 'archived') DEFAULT 'active'");
        } catch (\Exception $e) {
            // ignore if column doesn't exist
        }
    }

    public function down()
    {
        Schema::table('courses', function (Blueprint $table) {
            $columns = [
                'fee', 'duration_months', 'curriculum', 'prerequisites',
                'level', 'learning_outcomes', 'certificate_template',
                'category', 'max_students', 'passing_percentage',
                'instructor_name', 'instructor_email', 'course_image',
                'tags', 'is_featured'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('courses', $column)) {
                    $table->dropColumn($column);
                }
            }

            if (Schema::hasColumn('courses', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });

        try {
            DB::statement("ALTER TABLE courses MODIFY COLUMN status ENUM('active', 'inactive') DEFAULT 'active'");
        } catch (\Exception $e) {
            // ignore if column doesn't exist
        }
    }
};
