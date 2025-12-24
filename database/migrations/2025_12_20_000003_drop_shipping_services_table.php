<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('shipping_services');
    }

    public function down(): void
    {
        Schema::create('shipping_services', function (Blueprint $table) {
            $table->id('service_id');
            $table->foreignId('carrier_id')->constrained('shipping_carriers', 'carrier_id')->onDelete('cascade');
            $table->string('code')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }
};
