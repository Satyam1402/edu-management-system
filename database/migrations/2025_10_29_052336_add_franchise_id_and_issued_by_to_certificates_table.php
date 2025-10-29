<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('certificates', function (Blueprint $table) {
            // Add franchise_id (CRITICAL for franchise panel to work)
            if (!Schema::hasColumn('certificates', 'franchise_id')) {
                $table->unsignedBigInteger('franchise_id')->nullable()->after('course_id');
                $table->foreign('franchise_id')->references('id')->on('franchises')->onDelete('cascade');
            }

            // Add issued_by (track who issued the certificate)
            if (!Schema::hasColumn('certificates', 'issued_by')) {
                $table->unsignedBigInteger('issued_by')->nullable()->after('issued_at');
                $table->foreign('issued_by')->references('id')->on('users')->onDelete('set null');
            }

            // Add valid_until (optional - certificate expiry)
            if (!Schema::hasColumn('certificates', 'valid_until')) {
                $table->timestamp('valid_until')->nullable()->after('issued_by');
            }
        });
    }

    public function down()
    {
        Schema::table('certificates', function (Blueprint $table) {
            // Drop foreign keys first
            if (Schema::hasColumn('certificates', 'franchise_id')) {
                $table->dropForeign(['franchise_id']);
                $table->dropColumn('franchise_id');
            }

            if (Schema::hasColumn('certificates', 'issued_by')) {
                $table->dropForeign(['issued_by']);
                $table->dropColumn('issued_by');
            }

            if (Schema::hasColumn('certificates', 'valid_until')) {
                $table->dropColumn('valid_until');
            }
        });
    }
};
