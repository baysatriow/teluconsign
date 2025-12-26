<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('webhook_logs', function (Blueprint $table) {
            $table->id('webhook_log_id');
            $table->string('provider_code', 40);
            $table->string('event_type', 80)->nullable();
            $table->string('related_id', 80)->nullable();
            $table->json('payload')->nullable();
            $table->timestamp('received_at')->useCurrent();

            $table->index(['provider_code', 'received_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_logs');
    }
};
