<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallet_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('franchise_id')->constrained('franchises')->onDelete('cascade');
            $table->string('action', 100); // 'credit_added', 'debit_deducted', etc.
            $table->decimal('old_balance', 10, 2)->nullable();
            $table->decimal('new_balance', 10, 2)->nullable();
            $table->decimal('amount_changed', 10, 2)->nullable();
            $table->foreignId('performed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->json('details')->nullable();
            $table->timestamp('created_at')->useCurrent();
            
            $table->index(['franchise_id', 'created_at']);
            $table->index('performed_by');
            $table->index('action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_audit_logs');
    }
};
