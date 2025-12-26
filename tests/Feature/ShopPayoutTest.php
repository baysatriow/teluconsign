<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Address;
use App\Models\BankAccount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShopPayoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_seller_can_add_bank_account()
    {
        $seller = User::factory()->create(['role' => 'seller']);
        Address::factory()->create(['user_id' => $seller->user_id, 'is_shop_default' => true]);

        $response = $this->actingAs($seller)->post(route('shop.banks.store'), [
            'bank_name' => 'BCA',
            'account_no' => '1234567890',
            'account_name' => 'Seller Name'
        ]);

        $response->assertRedirect(); // Likely back
        $this->assertDatabaseHas('bank_accounts', [
            'user_id' => $seller->user_id,
            'bank_name' => 'BCA',
            'account_no' => '1234567890'
        ]);
    }

    public function test_seller_can_request_payout()
    {
        $seller = User::factory()->create(['role' => 'seller']);
        Address::factory()->create(['user_id' => $seller->user_id, 'is_shop_default' => true]);
        
        $bank = BankAccount::create([
            'user_id' => $seller->user_id,
            'bank_name' => 'BCA',
            'account_no' => '1234567890',
            'account_name' => 'Seller Name'
        ]);

        // Seed initial balance
        \App\Models\WalletLedger::create([
             'user_id' => $seller->user_id,
             'direction' => 'credit',
             'source_type' => 'adjustment',
             'amount' => 1000000,
             'balance_after' => 1000000,
             'memo' => 'Initial Balance',
             'posted_at' => now()
        ]);

        $response = $this->actingAs($seller)->post(route('shop.payouts.store'), [
            'amount' => 500000,
            'bank_account_id' => $bank->bank_account_id
        ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('payout_requests', [
            'seller_id' => $seller->user_id,
            'amount' => 500000,
            'status' => 'requested'
        ]);
    }
}
