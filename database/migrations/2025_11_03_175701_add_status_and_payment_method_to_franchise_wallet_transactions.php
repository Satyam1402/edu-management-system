<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('franchise_wallet_transactions', function (Blueprint $table) {
            // Add new columns if they don't exist
            if (!Schema::hasColumn('franchise_wallet_transactions', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('source');
            }
            if (!Schema::hasColumn('franchise_wallet_transactions', 'status')) {
                $table->enum('status', ['pending', 'completed', 'failed'])->default('completed')->after('payment_method');
            }
            if (!Schema::hasColumn('franchise_wallet_transactions', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('meta');
            }
        });
    }

    public function down()
    {
        Schema::table('franchise_wallet_transactions', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'status', 'completed_at']);
        });
    }
};
