<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->bigIncrements('bank_account_id');
            $table->unsignedBigInteger('user_id');
            $table->string('bank_name', 80);
            $table->string('account_name', 120);
            $table->string('account_no', 60);
            $table->boolean('is_default')->default(true);
            $table->timestamps();
            
            $table->foreign('user_id')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_accounts');
    }
};
