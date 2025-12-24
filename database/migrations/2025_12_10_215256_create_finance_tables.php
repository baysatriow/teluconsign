<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id('bank_account_id');
            $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->string('bank_name', 80);
            $table->string('account_name', 120);
            $table->string('account_no', 60);
            $table->boolean('is_default')->default(true);
            $table->timestamps();

            $table->unique(['user_id', 'account_no'], 'uniq_user_account');
        });

        Schema::create('wallet_ledger', function (Blueprint $table) {
            $table->id('wallet_ledger_id');
            $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->enum('direction', ['credit', 'debit']);
            $table->enum('source_type', ['order_settlement', 'payout', 'adjustment']);
            $table->unsignedBigInteger('source_id')->nullable();
            $table->decimal('amount', 14, 2);
            $table->decimal('balance_after', 14, 2);
            $table->string('memo')->nullable();
            $table->timestamp('posted_at')->useCurrent();
        });

        Schema::create('payout_requests', function (Blueprint $table) {
            $table->id('payout_request_id');
            $table->foreignId('seller_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->decimal('amount', 14, 2);
            $table->enum('status', ['requested', 'approved', 'rejected', 'paid', 'cancelled'])->default('requested');
            $table->foreignId('bank_account_id')->nullable()->constrained('bank_accounts', 'bank_account_id')->nullOnDelete();
            $table->timestamp('requested_at')->useCurrent();
            $table->foreignId('processed_by')->nullable()->constrained('users', 'user_id');
            $table->timestamp('processed_at')->nullable();
            $table->string('notes')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payout_requests');
        Schema::dropIfExists('wallet_ledger');
        Schema::dropIfExists('bank_accounts');
    }
};
