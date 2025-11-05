<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('certificate_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('certificate_requests', 'amount')) {
                $table->decimal('amount', 8, 2)->default(0.00)->after('certificate_type');
            }
        });
    }

    public function down()
    {
        Schema::table('certificate_requests', function (Blueprint $table) {
            if (Schema::hasColumn('certificate_requests', 'amount')) {
                $table->dropColumn('amount');
            }
        });
    }
};
