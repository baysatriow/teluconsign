<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->bigIncrements('payment_id');
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('provider_id')->nullable();
            $table->string('method_code', 60)->nullable();
            $table->decimal('amount', 14, 2);
            $table->char('currency', 3)->default('IDR');
            $table->enum('status', ['pending', 'capture', 'settlement', 'cancel', 'expire', 'deny', 'refund', 'chargeback'])->default('pending');
            $table->string('provider_txn_id', 80)->nullable();
            $table->string('provider_order_id', 80)->nullable();
            $table->json('raw_response')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            
            $table->foreign('order_id')
                  ->references('order_id')
                  ->on('orders')
                  ->onDelete('cascade');
                  
            $table->foreign('provider_id')
                  ->references('integration_provider_id')
                  ->on('integration_providers')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
