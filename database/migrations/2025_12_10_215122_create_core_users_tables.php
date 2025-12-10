<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Users Table
        Schema::create('users', function (Blueprint $table) {
            $table->id('user_id'); // Primary Key Custom Name sesuai SQL
            $table->string('name', 120);
            $table->string('username', 50)->unique()->nullable();
            $table->string('email', 191)->unique();
            $table->string('password');
            $table->enum('role', ['buyer', 'seller', 'admin'])->default('buyer');
            $table->enum('status', ['active', 'suspended', 'disabled'])->default('active');
            $table->string('photo_url')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes(); // Professional practice: Data user tidak langsung hilang permanen
        });

        // 2. Profiles Table (1-to-1 dengan Users)
        Schema::create('profiles', function (Blueprint $table) {
            // Kita gunakan user_id sebagai primary key juga untuk tabel ini
            $table->foreignId('user_id')->primary()->constrained('users', 'user_id')->onDelete('cascade');
            $table->string('phone', 30)->nullable();
            $table->string('bio')->nullable();
            $table->string('address')->nullable(); // Alamat singkat/KTP
            $table->timestamps();
        });

        // 3. Addresses Table (Alamat Pengiriman - 1 User punya banyak alamat)
        Schema::create('addresses', function (Blueprint $table) {
            $table->id('address_id');
            $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->string('label', 60); // Rumah, Kantor
            $table->string('recipient', 120);
            $table->string('phone', 30)->nullable();
            $table->string('city', 100);
            $table->string('province', 100);
            $table->string('postal_code', 20)->nullable();
            $table->text('full_address'); // Gabungan line1 & line2
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        // 4. Notifications Table
        Schema::create('notifications', function (Blueprint $table) {
            $table->id('notification_id');
            $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->string('type', 80); // system, order, promo
            $table->json('payload')->nullable(); // Data dinamis (link, message, id)
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('addresses');
        Schema::dropIfExists('profiles');
        Schema::dropIfExists('users');
    }
};
