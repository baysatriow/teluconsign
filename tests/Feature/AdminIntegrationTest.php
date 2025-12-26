<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ShippingCarrier;
use App\Models\IntegrationProvider;
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
        $id = DB::table('shipping_carriers')->insertGetId([
            'code' => 'jne',
            'name' => 'JNE',
            'provider_type' => 'rates', 
            'is_enabled' => true,
            'mode' => 'test'
        ]);

        $response = $this->actingAs($this->admin)->patch(route('admin.integrations.carrier.toggle', $id));

        $response->assertRedirect();
        $this->assertDatabaseHas('shipping_carriers', [
            'shipping_carrier_id' => $id,
            'is_enabled' => false
        ]);
    }

    public function test_admin_can_update_whatsapp_api_settings()
    {
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
