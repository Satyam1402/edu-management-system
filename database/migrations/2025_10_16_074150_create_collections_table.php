<?php
// database/migrations/2025_10_16_xxxxxx_create_collections_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('franchise_id')->constrained()->cascadeOnDelete();
            $table->string('period'); // 2025-01, 2025-02, etc.
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->decimal('commission_amount', 12, 2)->default(0);
            $table->decimal('net_amount', 12, 2)->default(0);
            $table->enum('status', ['pending', 'processed', 'paid'])->default('pending');
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            // Ensure one collection per franchise per period
            $table->unique(['franchise_id', 'period']);

            // Indexes
            $table->index(['franchise_id', 'status']);
            $table->index(['period', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collections');
    }
};
