<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('payment_methods', 'provider_id')) {
            Schema::table('payment_methods', function (Blueprint $table) {
                if (Schema::hasColumn('payment_methods', 'gateway_id')) {
                    $table->foreignId('provider_id')->nullable()->after('gateway_id');
                } else {
                     $table->foreignId('provider_id')->nullable();
                }
            });
        }

        try {
            Schema::table('payment_methods', function (Blueprint $table) {
                $table->dropForeign(['gateway_id']);
            });
        } catch (\Exception $e) { }

        try {
            Schema::table('payments', function (Blueprint $table) {
                 $table->dropForeign(['gateway_id']);
            });
        } catch (\Exception $e) { }

        Schema::dropIfExists('payment_gateways');

        if (Schema::hasColumn('payment_methods', 'gateway_id')) {
            Schema::table('payment_methods', function (Blueprint $table) {
                $table->dropColumn('gateway_id');
            });
        }

        if (DB::table('integration_providers')->where('code', 'midtrans')->doesntExist()) {
            $providerId = DB::table('integration_providers')->insertGetId([
                'code' => 'midtrans',
                'name' => 'Midtrans Payment Gateway',
                'created_at' => now(),
            ]);
            
            DB::table('integration_keys')->insert([
                'provider_id' => $providerId,
                'label' => 'Midtrans Production Key',
                'is_active' => true,
                'public_k' => null,
                'encrypted_k' => null,
                'meta_json' => json_encode(['is_production' => true]),
                'created_at' => now()
            ]);
        }

        $midtrans = DB::table('integration_providers')->where('code', 'midtrans')->first();
        if ($midtrans) {
            DB::table('payment_methods')->update(['provider_id' => $midtrans->integration_provider_id]);
        }

        Schema::table('payment_methods', function (Blueprint $table) use ($midtrans) {
             $table->foreign('provider_id')->references('integration_provider_id')->on('integration_providers')->onDelete('cascade');
        });
        
        Schema::table('payments', function (Blueprint $table) {
             $table->dropColumn('gateway_id');
             $table->foreignId('provider_id')->nullable()->after('order_id')->constrained('integration_providers', 'integration_provider_id');
        });
        if ($midtrans) {
            DB::table('payments')->update(['provider_id' => $midtrans->integration_provider_id]);
        }

    }

    public function down(): void
    {
    }
};
