<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Modify the status column to include 'paid'
        DB::statement("ALTER TABLE `certificate_requests`
            MODIFY COLUMN `status` ENUM('pending', 'approved', 'paid', 'completed', 'rejected')
            NOT NULL DEFAULT 'pending'");
    }

    public function down()
    {
        // Revert back to original ENUM values
        DB::statement("ALTER TABLE `certificate_requests`
            MODIFY COLUMN `status` ENUM('pending', 'approved', 'completed', 'rejected')
            NOT NULL DEFAULT 'pending'");
    }
};
