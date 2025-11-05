<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('certificate_requests', function (Blueprint $table) {
            // Add certificate_type column if it doesn't exist
            if (!Schema::hasColumn('certificate_requests', 'certificate_type')) {
                $table->string('certificate_type')->default('Course Completion Certificate')->after('course_id');
            }
        });
    }

    public function down()
    {
        Schema::table('certificate_requests', function (Blueprint $table) {
            if (Schema::hasColumn('certificate_requests', 'certificate_type')) {
                $table->dropColumn('certificate_type');
            }
        });
    }
};
