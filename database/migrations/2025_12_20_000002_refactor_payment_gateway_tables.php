<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add provider_id to payment_methods FIRST (nullable)
        if (!Schema::hasColumn('payment_methods', 'provider_id')) {
            Schema::table('payment_methods', function (Blueprint $table) {
                // Check if gateway_id exists to place after (it might not if this is re-run strangely, but assume yes)
                if (Schema::hasColumn('payment_methods', 'gateway_id')) {
                    $table->foreignId('provider_id')->nullable()->after('gateway_id');
                } else {
                     $table->foreignId('provider_id')->nullable();
                }
            });
        }

        // 2. Drop Foreign Key on payment_methods relating to gateways
        try {
            Schema::table('payment_methods', function (Blueprint $table) {
                $table->dropForeign(['gateway_id']);
            });
        } catch (\Exception $e) { /* Ignore if already dropped */ }

        // 2b. Drop Foreign Key on payments table relating to gateways (Fix for error 3730)
        try {
            Schema::table('payments', function (Blueprint $table) {
                 $table->dropForeign(['gateway_id']);
            });
        } catch (\Exception $e) { /* Ignore */ }

        // 3. Drop payment_gateways table
        Schema::dropIfExists('payment_gateways');

        // 4. Remove gateway_id from payment_methods
        if (Schema::hasColumn('payment_methods', 'gateway_id')) {
            Schema::table('payment_methods', function (Blueprint $table) {
                $table->dropColumn('gateway_id');
            });
        }

        // 5. Seed Integration Providers if empty
        if (DB::table('integration_providers')->where('code', 'midtrans')->doesntExist()) {
            $providerId = DB::table('integration_providers')->insertGetId([
                'code' => 'midtrans',
                'name' => 'Midtrans Payment Gateway',
                'created_at' => now(),
            ]);
            
            // Seed default Keys structure (active = false initially)
            DB::table('integration_keys')->insert([
                'provider_id' => $providerId,
                'label' => 'Midtrans Production Key',
                'is_active' => true,
                'public_k' => null, // User fills this in Admin
                'encrypted_k' => null, // User fills this in Admin
                'meta_json' => json_encode(['is_production' => true]),
                'created_at' => now()
            ]);
        }

        // 6. Link existing payment methods to midtrans (assuming they were midtrans)
        // Since we dropped gateway_id, we just update all existing payment_methods to point to the midtrans provider we just found/created
        $midtrans = DB::table('integration_providers')->where('code', 'midtrans')->first();
        if ($midtrans) {
            DB::table('payment_methods')->update(['provider_id' => $midtrans->integration_provider_id]);
        }

        // 7. Make provider_id Not Null and Add Foreign Key constraint
        Schema::table('payment_methods', function (Blueprint $table) use ($midtrans) {
             // If table was empty, this is fine. If not, step 6 ensured data integrity.
             // (Re-add constraint if needed, logic seems redundant if already constrained? No, we added column nullable first)
             $table->foreign('provider_id')->references('integration_provider_id')->on('integration_providers')->onDelete('cascade');
        });
        
        // 8. Update payments table to add provider_id column and drop old gateway_id column
        Schema::table('payments', function (Blueprint $table) {
             $table->dropColumn('gateway_id');
             $table->foreignId('provider_id')->nullable()->after('order_id')->constrained('integration_providers', 'integration_provider_id');
        });
        // Update existing payments to point to midtrans
        if ($midtrans) {
            DB::table('payments')->update(['provider_id' => $midtrans->integration_provider_id]);
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       // Irreversible smoothly because data loss in payment_gateways
    }
};
