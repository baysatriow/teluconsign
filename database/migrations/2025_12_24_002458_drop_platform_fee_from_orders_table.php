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
        if (Schema::hasColumn('orders', 'platform_fee')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('platform_fee');
            });
        }

        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'platform_fee_buyer')) {
                $table->decimal('platform_fee_buyer', 10, 2)->default(0)->after('shipping_cost');
            }
            if (!Schema::hasColumn('orders', 'platform_fee_seller')) {
                $table->decimal('platform_fee_seller', 10, 2)->default(0)->after('platform_fee_buyer');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('platform_fee', 10, 2)->default(0)->after('shipping_cost');
            $table->dropColumn(['platform_fee_buyer', 'platform_fee_seller']);
        });
    }
};
