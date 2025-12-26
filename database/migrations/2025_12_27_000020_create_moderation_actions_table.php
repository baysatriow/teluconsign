<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('moderation_actions', function (Blueprint $table) {
            $table->id('moderation_action_id');
            $table->foreignId('admin_id')->constrained('users', 'user_id')->onDelete('restrict');
            $table->enum('target_type', ['product', 'order', 'review']);
            $table->unsignedBigInteger('target_id');
            $table->enum('action', ['takedown', 'suspend', 'restore', 'hide', 'unhide']);
            $table->string('reason')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['target_type', 'target_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('moderation_actions');
    }
};
