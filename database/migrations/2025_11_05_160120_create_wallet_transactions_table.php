<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('franchise_wallet_id')->constrained('franchise_wallets')->onDelete('cascade');
            $table->enum('type', ['credit', 'debit']); // credit = add money, debit = spend money
            $table->decimal('amount', 10, 2);
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->decimal('balance_after', 10, 2)->nullable();
            $table->string('transaction_id')->nullable()->unique();
            $table->string('payment_method')->nullable(); // razorpay, upi, admin, etc.
            $table->json('metadata')->nullable(); // For storing extra data
            $table->timestamps();

            // Indexes for faster queries
            $table->index('franchise_wallet_id');
            $table->index('type');
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
