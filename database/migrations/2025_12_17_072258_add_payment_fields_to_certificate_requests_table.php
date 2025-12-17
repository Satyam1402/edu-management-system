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
        Schema::table('certificate_requests', function (Blueprint $table) {
            // Add payment tracking columns
            $table->string('payment_status')->default('pending')->after('status');
            $table->timestamp('paid_at')->nullable()->after('payment_status');
            $table->unsignedBigInteger('wallet_transaction_id')->nullable()->after('paid_at');

            // Add certificate processing columns
            $table->unsignedBigInteger('processed_by')->nullable()->after('admin_notes');
            $table->timestamp('processed_at')->nullable()->after('processed_by');
            $table->string('certificate_number')->nullable()->after('processed_at');
            $table->timestamp('issued_date')->nullable()->after('certificate_number');

            // Add foreign key for wallet transaction
            $table->foreign('wallet_transaction_id')
                  ->references('id')
                  ->on('wallet_transactions')
                  ->onDelete('set null');

            // Add foreign key for processed_by
            $table->foreign('processed_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('certificate_requests', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['wallet_transaction_id']);
            $table->dropForeign(['processed_by']);

            // Drop columns
            $table->dropColumn([
                'payment_status',
                'paid_at',
                'wallet_transaction_id',
                'processed_by',
                'processed_at',
                'certificate_number',
                'issued_date'
            ]);
        });
    }
};
