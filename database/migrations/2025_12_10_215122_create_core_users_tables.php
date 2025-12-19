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
            $table->id('user_id');
            $table->string('name', 120);
            $table->string('username', 50)->unique()->nullable();
            $table->string('email', 191)->unique();
            $table->string('password');
            $table->enum('role', ['buyer', 'seller', 'admin'])->default('buyer');
            $table->enum('status', ['active', 'suspended', 'disabled'])->default('active');
            $table->string('photo_url')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Profiles Table
        Schema::create('profiles', function (Blueprint $table) {
            $table->foreignId('user_id')->primary()->constrained('users', 'user_id')->onDelete('cascade');
            $table->string('phone', 30)->nullable();
            $table->string('bio')->nullable();
            $table->string('address')->nullable();
            $table->timestamps();
        });

        // 3. Addresses Table (REVISI FINAL)
        Schema::create('addresses', function (Blueprint $table) {
            $table->id('address_id');
            $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');

            // Info Penerima
            $table->string('label', 60); // Rumah, Kantor
            $table->string('recipient', 120);
            $table->string('phone', 30);

            // Data Wilayah (Dari API/Pilihan)
            $table->string('province', 100);  // Jawa Barat
            $table->string('city', 100);      // Bandung
            $table->string('district', 100);  // Bojongsoang (Kecamatan)
            $table->string('village', 100);   // Lengkong (Desa/Kelurahan)
            $table->string('postal_code', 10);

            // Data Manual (Ketik Sendiri)
            $table->text('detail_address');   // Jl. Telekomunikasi No. 1, RT 02/RW 03

            $table->string('country', 2)->default('ID');
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        // 4. Notifications Table
        Schema::create('notifications', function (Blueprint $table) {
            $table->id('notification_id');
            $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->string('type', 80);
            $table->json('payload')->nullable();
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
