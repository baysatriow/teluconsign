<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

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
        | Catatan: API Key sebaiknya dipindah ke .env
        | (saat ini hardcoded untuk kebutuhan verifikasi)
        */
        $apiKey  = 'd5LxeDvW8f8033e2b179a590DEyT4Xf1';
        $baseUrl = 'https://rajaongkir.komerce.id/api/v1/destination/domestic-destination';

        try {
            /*
            | >>> PROXY REQUEST (cURL)
            | Menggunakan raw cURL untuk menyesuaikan
            | dengan konfigurasi SSL & header lokal
            */
            $ch  = curl_init();
            $url = $baseUrl . '?search=' . urlencode($query);

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
