<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| LOCATION CONTROLLER
|--------------------------------------------------------------------------
| Proxy pencarian lokasi ke API Komerce (RajaOngkir)
| Tujuan:
| - Menyembunyikan API Key dari frontend
| - Menjaga konsistensi & keamanan request
|--------------------------------------------------------------------------
*/
class LocationController extends Controller
{
    /*
    |----------------------------------------------------------------------
    | SEARCH LOCATION
    |----------------------------------------------------------------------
    | - Validasi minimal keyword
    | - Forward request ke API Komerce
    | - Return response JSON ke frontend
    |----------------------------------------------------------------------
    */
    public function search(Request $request)
    {
        /*
        | >>> VALIDASI INPUT
        | Keyword minimal 3 karakter untuk efisiensi API
        */
        $query = $request->input('q');

        if (!$query || strlen($query) < 3) {
            return response()->json([]);
        }

        /*
        | >>> KONFIGURASI API
        | Ambil dari Database (Integration Tables) sesuai format BaseIntegrationService
        */
        $provider = DB::table('integration_providers')->where('code', 'rajaongkir')->first();
        $key = $provider ? DB::table('integration_keys')
                ->where('provider_id', $provider->integration_provider_id)
                ->where('is_active', true)
                ->first() : null;

        // Fallback jika tidak ada konfigurasi di DB
        if (!$key) {
            return response()->json([]);
        }

        $config  = json_decode($key->meta_json ?? '{}', true);
        $apiKey  = $key->public_k;
        
        // Base URL dari config (biasanya https://rajaongkir.komerce.id/api/v1)
        // Kita append endpoint specific
        $baseApiUrl = $config['base_url'] ?? 'https://rajaongkir.komerce.id/api/v1';
        $targetUrl  = $baseApiUrl . '/destination/domestic-destination';

        try {
            /*
            | >>> PROXY REQUEST (cURL)
            | Menggunakan raw cURL untuk menyesuaikan
            | dengan konfigurasi SSL & header lokal
            */
            $ch  = curl_init();
            $url = $targetUrl . '?search=' . urlencode($query);

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ["key: $apiKey"]);

            $result = curl_exec($ch);

            /*
            | >>> ERROR HANDLING
            | Tangkap error cURL sebelum parsing data
            */
            if (curl_errno($ch)) {
                return response()->json([
                    'error' => curl_error($ch)
                ], 500);
            }

            curl_close($ch);

            /*
            | >>> PARSING RESPONSE
            | Ambil hanya data lokasi jika response valid
            */
            $data = json_decode($result, true);

            if (isset($data['status']) && $data['status'] === 'error') {
                return response()->json([], 200);
            }

            return response()->json($data['data'] ?? []);

        } catch (\Exception $e) {
            /*
            | >>> FAILSAFE
            | Menangkap error tak terduga
            */
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
