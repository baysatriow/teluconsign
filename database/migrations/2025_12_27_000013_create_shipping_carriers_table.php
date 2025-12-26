<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_carriers', function (Blueprint $table) {
            $table->bigIncrements('shipping_carrier_id');
            $table->string('code', 40);
            $table->string('name', 120);
            $table->enum('provider_type', ['aggregator', 'tracking', 'rates']);
            $table->enum('mode', ['test', 'live'])->default('test');
            $table->boolean('is_enabled')->default(false);
            $table->json('config_json')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_carriers');
    }
};
