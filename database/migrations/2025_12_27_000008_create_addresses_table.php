<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->bigIncrements('address_id');
            $table->unsignedBigInteger('user_id');
            $table->string('label', 60);
            $table->string('recipient', 120);
            $table->string('phone', 30);
            $table->string('province', 100);
            $table->string('city', 100);
            $table->string('district', 100);
            $table->string('village', 100);
            $table->string('postal_code', 10);
            $table->unsignedBigInteger('location_id')->nullable();
            $table->text('detail_address');
            $table->string('country', 2)->default('ID');
            $table->boolean('is_default')->default(false);
            $table->boolean('is_shop_default')->default(false);
            $table->timestamps();
            
            $table->foreign('user_id')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
