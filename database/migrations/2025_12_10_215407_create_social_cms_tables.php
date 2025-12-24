<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id('review_id');
            $table->foreignId('product_id')->constrained('products', 'product_id')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->tinyInteger('rating');
            $table->text('comment')->nullable();
            $table->enum('status', ['visible', 'hidden'])->default('visible');
            $table->timestamps();
        });

        Schema::create('banner_slides', function (Blueprint $table) {
            $table->id('banner_slide_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image_path');
            $table->string('link_url')->nullable();
            $table->boolean('is_active')->default(false);
            $table->integer('sort_order')->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users', 'user_id')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users', 'user_id')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('integration_providers', function (Blueprint $table) {
            $table->id('integration_provider_id');
            $table->string('code', 40)->unique();
            $table->string('name', 120);
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('integration_keys', function (Blueprint $table) {
            $table->id('integration_key_id');
            $table->foreignId('provider_id')->constrained('integration_providers', 'integration_provider_id')->onDelete('cascade');
            $table->string('label', 120);
            $table->boolean('is_active')->default(true);
            $table->text('public_k')->nullable();
            $table->text('encrypted_k')->nullable();
            $table->json('meta_json')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('moderation_actions', function (Blueprint $table) {
            $table->id('moderation_action_id');
            $table->foreignId('admin_id')->constrained('users', 'user_id')->restrictOnDelete();
            $table->enum('target_type', ['product', 'order', 'review']);
            $table->unsignedBigInteger('target_id');
            $table->enum('action', ['takedown', 'suspend', 'restore', 'hide', 'unhide']);
            $table->string('reason')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['target_type', 'target_id']);
        });

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
        Schema::dropIfExists('moderation_actions');
        Schema::dropIfExists('integration_keys');
        Schema::dropIfExists('integration_providers');
        Schema::dropIfExists('banner_slides');
        Schema::dropIfExists('reviews');
    }
};
