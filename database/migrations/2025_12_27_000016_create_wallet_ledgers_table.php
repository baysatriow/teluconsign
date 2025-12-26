<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallet_ledgers', function (Blueprint $table) {
            $table->bigIncrements('wallet_ledger_id');
            $table->unsignedBigInteger('user_id');
            $table->enum('direction', ['credit', 'debit']);
            $table->string('source_type', 255);
            $table->string('source_id', 255)->nullable();
            $table->decimal('amount', 14, 2);
            $table->decimal('balance_after', 14, 2);
            $table->text('memo')->nullable();
            $table->timestamp('posted_at');
            
            $table->foreign('user_id')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_ledgers');
    }
};
