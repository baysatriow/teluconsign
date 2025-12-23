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
        Schema::table('orders', function (Blueprint $table) {
            // Change enum to string to support 'delivered' and future statuses
            $table->string('status', 50)->default('pending')->change();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Revert to enum (make sure to include all used values or data might be lost/truncated if strict)
            // But for down, we will try to best effort revert
            $table->enum('status', ['pending', 'paid', 'processing', 'shipped', 'completed', 'cancelled', 'refunded'])->default('pending')->change();
        });
    }
};
