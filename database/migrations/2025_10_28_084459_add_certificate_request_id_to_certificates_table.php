<?php

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
        Schema::table('certificates', function (Blueprint $table) {
            // Add the missing certificate_request_id column
            $table->unsignedBigInteger('certificate_request_id')->nullable()->after('id');

            // Add foreign key constraint
            $table->foreign('certificate_request_id')
                  ->references('id')
                  ->on('certificate_requests')
                  ->onDelete('cascade');

            // Add index for performance
            $table->index('certificate_request_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['certificate_request_id']);

            // Drop the column
            $table->dropColumn('certificate_request_id');
        });
    }
};
