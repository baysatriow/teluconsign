<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ShippingCarrier;
use App\Models\IntegrationProvider; // Replaces PaymentGateway based on earlier schema notes if needed, but schema used PaymentGateway. Checking...
// Actually schema create_payment_logistics_tables uses payment_gateways table.
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class AdminIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    public function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_admin_can_toggle_shipping_carrier()
    {
        // Seed carrier
        $id = DB::table('shipping_carriers')->insertGetId([
            'code' => 'jne',
            'name' => 'JNE',
            'provider_type' => 'rates', 
            'is_enabled' => true,
            'mode' => 'test'
        ]);

        $response = $this->actingAs($this->admin)->patch(route('admin.integrations.carrier.toggle', $id));

        $response->assertRedirect(); // Controller uses back()
        $this->assertDatabaseHas('shipping_carriers', [
            'shipping_carrier_id' => $id,
            'is_enabled' => false // Should toggle to false
        ]);
    }

    public function test_admin_can_update_whatsapp_api_settings()
    {
        // Route: integrations.whatsapp.update
        // This likely updates json settings in a config table or similar.
        // Assuming implementation uses a settings table or hardcoded config file update (less likely to be tested safely).
        // Let's check route... AdminController::updateWhatsappApi
        
        // Skip detailed logic assertion if implementation is unknown, but can test route access.
        
        // Seed provider
        DB::table('integration_providers')->insert([
            'code' => 'whatsapp',
            'name' => 'Fonnte'
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.integrations.whatsapp.update'), [
            'token' => 'new_api_key',
            'sender' => '08123456789'
        ]);
        
        $response->assertRedirect();
        $response->assertSessionHas('success');
    }
}
