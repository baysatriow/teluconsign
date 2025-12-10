<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Reviews (Ulasan Produk)
        Schema::create('reviews', function (Blueprint $table) {
            $table->id('review_id');
            $table->foreignId('product_id')->constrained('products', 'product_id')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->tinyInteger('rating'); // 1-5
            $table->text('comment')->nullable();
            $table->enum('status', ['visible', 'hidden'])->default('visible');
            $table->timestamps();
        });

        // 2. Banner Slides (Slider Homepage)
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

        // 3. Integration Providers (Daftar Provider: Midtrans, Fonnte, RajaOngkir)
        Schema::create('integration_providers', function (Blueprint $table) {
            $table->id('integration_provider_id');
            $table->string('code', 40)->unique(); // e.g., 'midtrans', 'whatsapp'
            $table->string('name', 120);
            $table->timestamp('created_at')->useCurrent();
        });

        // 4. Integration Keys (API Keys untuk setiap provider)
        Schema::create('integration_keys', function (Blueprint $table) {
            $table->id('integration_key_id');
            $table->foreignId('provider_id')->constrained('integration_providers', 'integration_provider_id')->onDelete('cascade');
            $table->string('label', 120); // e.g., 'Midtrans Production', 'WA Marketing'
            $table->boolean('is_active')->default(true);
            $table->text('public_k')->nullable(); // Public Key / Client Key
            $table->text('encrypted_k')->nullable(); // Secret Key / Server Key (Encrypted)
            $table->json('meta_json')->nullable(); // Config tambahan
            $table->timestamp('created_at')->useCurrent();
        });

        // 5. Moderation Actions (Log tindakan admin terhadap konten)
        Schema::create('moderation_actions', function (Blueprint $table) {
            $table->id('moderation_action_id');
            $table->foreignId('admin_id')->constrained('users', 'user_id')->restrictOnDelete();
            $table->enum('target_type', ['product', 'order', 'review']);
            $table->unsignedBigInteger('target_id'); // ID dari target (product_id, dll)
            $table->enum('action', ['takedown', 'suspend', 'restore', 'hide', 'unhide']);
            $table->string('reason')->nullable();
            $table->timestamp('created_at')->useCurrent();

            // Index untuk mempercepat pencarian histori moderasi
            $table->index(['target_type', 'target_id']);
        });

        // 6. Webhook Logs (Log request dari pihak ketiga)
        Schema::create('webhook_logs', function (Blueprint $table) {
            $table->id('webhook_log_id');
            $table->string('provider_code', 40); // e.g., 'midtrans'
            $table->string('event_type', 80)->nullable(); // e.g., 'payment_status'
            $table->string('related_id', 80)->nullable(); // e.g., Order ID
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
