<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payout_requests', function (Blueprint $table) {
            $table->bigIncrements('payout_request_id');
            $table->unsignedBigInteger('seller_id');
            $table->decimal('amount', 14, 2);
            $table->enum('status', ['requested', 'approved', 'rejected', 'paid', 'cancelled'])->default('requested');
            $table->unsignedBigInteger('bank_account_id')->nullable();
            $table->timestamp('requested_at')->useCurrent();
            $table->unsignedBigInteger('processed_by')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->string('notes', 255)->nullable();
            $table->timestamps();
            
            $table->foreign('seller_id')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('cascade');
                  
            $table->foreign('bank_account_id')
                  ->references('bank_account_id')
                  ->on('bank_accounts')
                  ->onDelete('set null');
                  
            $table->foreign('processed_by')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payout_requests');
    }
};
