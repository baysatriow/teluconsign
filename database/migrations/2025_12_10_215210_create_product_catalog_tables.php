<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id('category_id');
            $table->foreignId('parent_id')->nullable()->constrained('categories', 'category_id')->nullOnDelete();
            $table->string('name', 120);
            $table->string('slug', 160)->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id('product_id');
            $table->foreignId('seller_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained('categories', 'category_id')->nullOnDelete();
            $table->string('title', 160);
            $table->text('description')->nullable();
            $table->decimal('price', 14, 2);
            $table->integer('stock')->default(1);
            $table->enum('condition', ['new', 'used'])->default('used');
            $table->string('location', 120)->nullable();
            $table->enum('status', ['active', 'sold', 'archived', 'suspended'])->default('active');
            $table->string('main_image')->nullable(); 
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('product_images', function (Blueprint $table) {
            $table->id('product_image_id');
            $table->foreignId('product_id')->constrained('products', 'product_id')->onDelete('cascade');
            $table->string('url');
            $table->boolean('is_primary')->default(false);
            $table->integer('sort_order')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
    }
};
