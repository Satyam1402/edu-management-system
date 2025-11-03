<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFranchiseWalletsTable extends Migration
{
    public function up()
    {
        Schema::create('franchise_wallets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('franchise_id')->unique();
            $table->decimal('balance', 12, 2)->default(0);
            $table->timestamps();

            $table->foreign('franchise_id')->references('id')->on('franchises')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('franchise_wallets');
    }
}

