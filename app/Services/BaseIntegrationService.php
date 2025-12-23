<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

/*
|--------------------------------------------------------------------------
| BASE INTEGRATION SERVICE
|--------------------------------------------------------------------------
*/
class BaseIntegrationService
{
    protected function getCredential(string $providerCode): ?object
    {
        $provider = DB::table('integration_providers')
            ->where('code', $providerCode)
            ->first();

        if (!$provider) {
            return null;
        }

        $key = DB::table('integration_keys')
            ->where('provider_id', $provider->integration_provider_id)
            ->where('is_active', true)
            ->first();

        if (!$key) {
            return null;
        }

        if (!empty($key->encrypted_k)) {
            try {
                $key->secret_key = Crypt::decryptString($key->encrypted_k);
            } catch (\Exception $e) {
                $key->secret_key = $key->encrypted_k;
            }
        } else {
            $key->secret_key = null;
        }

        $key->config = json_decode($key->meta_json ?? '{}', true);

        return $key;
    }
}
