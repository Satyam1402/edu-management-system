<?php
// database/migrations/2025_10_17_062628_add_additional_fields_to_students_table.php - FINAL FIXED VERSION
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {
            // Add student_id column first (nullable, not unique yet)
            $table->string('student_id')->nullable()->after('id');

            // Personal information
            $table->date('date_of_birth')->nullable()->after('phone');
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('date_of_birth');
            $table->text('address')->nullable()->after('gender');
            $table->string('city', 100)->nullable()->after('address');
            $table->string('state', 100)->nullable()->after('city');
            $table->string('pincode', 10)->nullable()->after('state');

            // Guardian information
            $table->string('guardian_name')->nullable()->after('pincode');
            $table->string('guardian_phone', 15)->nullable()->after('guardian_name');

            // Academic information
            $table->foreignId('course_id')->nullable()->constrained()->onDelete('set null')->after('franchise_id');
            $table->date('enrollment_date')->nullable()->after('course_id');
            $table->string('batch')->nullable()->after('enrollment_date');

            // Additional fields
            $table->text('notes')->nullable()->after('batch');
            $table->string('profile_photo')->nullable()->after('notes');

            // Soft deletes
            $table->softDeletes();
        });

        // POPULATE STUDENT IDs FOR EXISTING RECORDS
        $students = DB::table('students')->whereNull('student_id')->orWhere('student_id', '')->get();
        foreach ($students as $student) {
            $studentId = 'STU' . str_pad($student->id, 6, '0', STR_PAD_LEFT);
            DB::table('students')->where('id', $student->id)->update(['student_id' => $studentId]);
        }

        // NOW ADD UNIQUE CONSTRAINT AFTER POPULATING DATA
        Schema::table('students', function (Blueprint $table) {
            $table->unique('student_id');
        });

        // Update status enum to include more options
        DB::statement("ALTER TABLE students MODIFY COLUMN status ENUM('active', 'inactive', 'graduated', 'dropped', 'suspended') DEFAULT 'active'");
    }

    public function down()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropUnique(['student_id']);
            $table->dropColumn([
                'student_id', 'date_of_birth', 'gender', 'address', 'city',
                'state', 'pincode', 'guardian_name', 'guardian_phone',
                'course_id', 'enrollment_date', 'batch', 'notes', 'profile_photo'
            ]);
            $table->dropSoftDeletes();
        });

        // Revert status enum
        DB::statement("ALTER TABLE students MODIFY COLUMN status ENUM('active', 'inactive') DEFAULT 'active'");
    }
};
