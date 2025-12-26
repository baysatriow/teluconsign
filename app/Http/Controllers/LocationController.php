<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class LocationController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('q');

        if (!$query || strlen($query) < 3) {
            return response()->json([]);
        }

        $provider = DB::table('integration_providers')->where('code', 'rajaongkir')->first();
        $key = $provider ? DB::table('integration_keys')
                ->where('provider_id', $provider->integration_provider_id)
                ->where('is_active', true)
                ->first() : null;

        if (!$key) {
            return response()->json([]);
        }

        $config  = json_decode($key->meta_json ?? '{}', true);
        $apiKey  = $key->public_k;
        
        $baseApiUrl = $config['base_url'] ?? 'https://rajaongkir.komerce.id/api/v1';
        $targetUrl  = $baseApiUrl . '/destination/domestic-destination';

        try {
            $response = Http::withHeaders([
                'key' => $apiKey
            ])->get($targetUrl, [
                'search' => $query
            ]);

            if ($response->failed()) {
                return response()->json([
                    'error' => $response->body()
                ], 500);
            }

            $data = $response->json();

            if (isset($data['status']) && $data['status'] === 'error') {
                 return response()->json([], 200);
            }

            return response()->json($data['data'] ?? []);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}