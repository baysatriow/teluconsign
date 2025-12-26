<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('product_id');
            $table->unsignedBigInteger('seller_id');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('title', 160);
            $table->string('slug', 191)->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 14, 2);
            $table->integer('weight')->default(0);
            $table->integer('stock')->default(1);
            $table->enum('condition', ['new', 'used'])->default('used');
            $table->enum('status', ['active', 'sold', 'archived', 'suspended'])->default('active');
            $table->text('suspension_reason')->nullable();
            $table->string('main_image', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('seller_id')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('cascade');
                  
            $table->foreign('category_id')
                  ->references('category_id')
                  ->on('categories')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
