<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LocationController extends Controller
{
    /**
     * Search for a location via Komerce API.
     * Proxies the request to avoid exposing API key on frontend.
     */
    public function search(Request $request)
    {
        $query = $request->input('q');

        if (!$query || strlen($query) < 3) {
            return response()->json([]);
        }

        $apiKey = 'd5LxeDvW8f8033e2b179a590DEyT4Xf1'; // Should be in .env but verifying first
        $baseUrl = 'https://rajaongkir.komerce.id/api/v1/destination/domestic-destination';

        try {
            // Using raw curl to match the proven working code in probe_api.php
            // Laravel Http client often has issues with specific SSL/Header configs on local envs
            
            $ch = curl_init();
            $url = $baseUrl . "?search=" . urlencode($query);
            
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ["key: $apiKey"]);
            
            $result = curl_exec($ch);
            
            if (curl_errno($ch)) {
                return response()->json(['error' => curl_error($ch)], 500);
            }
            
            curl_close($ch);
            
            $data = json_decode($result, true);
            
            if (isset($data['status']) && $data['status'] === 'error') {
                 return response()->json([], 200);
            }

            return response()->json($data['data'] ?? []);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
