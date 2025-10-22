<?php
// database/migrations/XXXX_XX_XX_XXXXXX_add_additional_fields_to_franchises_table.php

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
        Schema::table('franchises', function (Blueprint $table) {
            // Address Information
            $table->string('city', 100)->nullable()->after('address');
            $table->string('state', 100)->nullable()->after('city');
            $table->string('pincode', 10)->nullable()->after('state');
            
            // Additional Franchise Information
            $table->string('contact_person', 100)->nullable()->after('pincode');
            $table->date('established_date')->nullable()->after('contact_person');
            $table->text('notes')->nullable()->after('established_date');
            
            // Make sure status column exists and has proper values
            if (!Schema::hasColumn('franchises', 'status')) {
                $table->enum('status', ['active', 'inactive', 'suspended'])->default('active')->after('notes');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('franchises', function (Blueprint $table) {
            $table->dropColumn([
                'address',
                'city', 
                'state',
                'pincode',
                'contact_person',
                'established_date',
                'notes'
            ]);
        });
    }
};
