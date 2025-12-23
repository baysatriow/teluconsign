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
        Schema::create('wallet_ledgers', function (Blueprint $table) {
            $table->id('wallet_ledger_id');
            $table->foreignId('user_id')->constrained('users', 'user_id');
            $table->enum('direction', ['credit', 'debit']);
            $table->string('source_type'); // 'order', 'payout', 'refund', 'adjustment'
            $table->string('source_id')->nullable();
            $table->decimal('amount', 14, 2);
            $table->decimal('balance_after', 14, 2);
            $table->text('memo')->nullable();
            $table->timestamp('posted_at');
            // No timestamps() because 'posted_at' handles it, and we don't need update checks
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_ledgers');
    }
};
