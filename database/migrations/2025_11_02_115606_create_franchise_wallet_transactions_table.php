<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFranchiseWalletTransactionsTable extends Migration
{
    public function up()
    {
        Schema::create('franchise_wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('franchise_id');
            $table->enum('type', ['credit', 'debit']);
            $table->decimal('amount', 12, 2);
            $table->string('source');
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->foreign('franchise_id')->references('id')->on('franchises')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('franchise_wallet_transactions');
    }
}

