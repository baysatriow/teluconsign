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
        Schema::create('integration_keys', function (Blueprint $table) {
            $table->bigIncrements('integration_key_id');
            $table->unsignedBigInteger('provider_id');
            $table->string('label', 120);
            $table->boolean('is_active')->default(true);
            $table->text('public_k')->nullable();
            $table->text('encrypted_k')->nullable();
            $table->json('meta_json')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            
            $table->foreign('provider_id')
                  ->references('integration_provider_id')
                  ->on('integration_providers')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('integration_keys');
    }
};
