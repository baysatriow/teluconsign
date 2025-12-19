<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Exception;

class BaseIntegrationService
{
    /**
     * Mengambil kredensial aktif berdasarkan kode provider.
     * @param string $providerCode contoh: 'midtrans', 'whatsapp'
     * @return object|null Data credentials
     * @throws Exception Jika provider tidak ditemukan atau tidak aktif
     */
    protected function getCredential(string $providerCode)
    {
        // 1. Cari Provider ID
        $provider = DB::table('integration_providers')
            ->where('code', $providerCode)
            ->first();

        if (!$provider) {
            // Kita return null agar service anak (FonnteService) bisa handle fallback (simulasi)
            return null;
        }

        // 2. Cari Key yang Aktif
        $key = DB::table('integration_keys')
            ->where('provider_id', $provider->integration_provider_id)
            ->where('is_active', true)
            ->first();

        if (!$key) {
            return null;
        }

        // 3. Decrypt Server Key jika ada (karena di database encrypted_k)
        if (!empty($key->encrypted_k)) {
            try {
                $key->secret_key = Crypt::decryptString($key->encrypted_k);
            } catch (\Exception $e) {
                // Fallback jika decrypt gagal (misal data dummy manual)
                $key->secret_key = $key->encrypted_k;
            }
        } else {
            $key->secret_key = null;
        }

        // 4. Decode Meta JSON
        $key->config = json_decode($key->meta_json ?? '{}', true);

        return $key;
    }
}
