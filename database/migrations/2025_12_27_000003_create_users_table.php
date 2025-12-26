<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('user_id');
            $table->string('name', 120);
            $table->string('username', 50)->nullable()->unique();
            $table->string('email', 191)->unique();
            $table->string('password', 255);
            $table->enum('role', ['buyer', 'seller', 'admin'])->default('buyer');
            $table->enum('status', ['active', 'suspended', 'disabled'])->default('active');
            $table->string('otp_code', 6)->nullable();
            $table->timestamp('otp_expires_at')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->string('photo_url', 255)->nullable();
            $table->string('remember_token', 100)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
