<?php
// database/migrations/xxxx_add_pricing_fields_to_courses_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            // Add new pricing fields after the existing 'fee' column
            $table->decimal('discount_fee', 10, 2)->nullable()->after('fee')->comment('Discounted fee if applicable');
            $table->boolean('is_free')->default(false)->after('discount_fee')->comment('Is this course free');
            $table->decimal('franchise_fee', 10, 2)->nullable()->after('is_free')->comment('Special fee for franchise students');
            $table->text('fee_notes')->nullable()->after('franchise_fee')->comment('Additional notes about pricing');
        });
    }

    public function down()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['discount_fee', 'is_free', 'franchise_fee', 'fee_notes']);
        });
    }
};
