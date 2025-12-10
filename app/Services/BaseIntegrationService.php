<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Exception;

class BaseIntegrationService
{
    /**
     * Mengambil kredensial aktif berdasarkan kode provider.
     * * @param string $providerCode contoh: 'midtrans', 'fonnte'
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
            throw new Exception("Provider dengan kode '{$providerCode}' tidak ditemukan di database.");
        }

        // 2. Cari Key yang Aktif
        $key = DB::table('integration_keys')
            ->where('provider_id', $provider->integration_provider_id)
            ->where('is_active', true)
            ->first();

        if (!$key) {
            // Fallback: Bisa return null atau throw error tergantung kebutuhan
            // Disini kita throw agar developer sadar belum setup key
            throw new Exception("Tidak ada API Key aktif untuk provider '{$provider->name}'. Harap konfigurasi di Admin Panel.");
        }

        // 3. Decrypt Server Key jika ada (karena di database encrypted_k)
        // Asumsi: Anda menyimpan 'encrypted_k' menggunakan Crypt::encryptString() saat simpan data
        if (!empty($key->encrypted_k)) {
            try {
                // Jika error decrypt (misal data dummy manual), kembalikan raw value saja untuk dev environment
                $key->secret_key = Crypt::decryptString($key->encrypted_k);
            } catch (\Exception $e) {
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
