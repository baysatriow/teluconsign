<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RajaOngkirService extends BaseIntegrationService
{
    protected string $providerCode = 'rajaongkir';

    /**
     * Cek Ongkos Kirim (Domestic Cost)
     * Menggunakan Endpoint Baru: /calculate/domestic-cost
     * * @param int|string $origin ID Kota/Kecamatan asal
     * @param string $originType 'city' atau 'subdistrict' (Default Komerce biasanya butuh detail, gunakan subdistrict untuk akurasi)
     * @param int|string $destination ID Kota/Kecamatan tujuan
     * @param string $destinationType 'city' atau 'subdistrict'
     * @param int $weight Berat dalam gram
     * @param string $courier Kode kurir (jne, siicepat, jnt, dll)
     */
    public function checkCost($origin, $originType, $destination, $destinationType, $weight, $courier)
    {
        try {
            $cred = $this->getCredential($this->providerCode);

            // Base URL baru Komerce
            $baseUrl = $cred->config['base_url'] ?? 'https://rajaongkir.komerce.id/api/v1';

            // Payload untuk /calculate/domestic-cost
            $payload = [
                'origin' => $origin,
                'originType' => $originType,
                'destination' => $destination,
                'destinationType' => $destinationType,
                'weight' => $weight,
                'courier' => $courier,
            ];

            $response = Http::withHeaders([
                'key' => $cred->public_k, // API Key Komerce
            ])->post("{$baseUrl}/calculate/domestic-cost", $payload);

            if ($response->failed()) {
                $body = $response->json();
                // Komerce kadang mengembalikan error di meta.message atau data.message
                $errorMsg = $body['meta']['message'] ?? $body['rajaongkir']['status']['description'] ?? 'Gagal koneksi ke API Komerce.';

                Log::error("RajaOngkir Komerce Error [{$courier}]: " . $errorMsg, $payload);
                return ['status' => false, 'message' => $errorMsg];
            }

            $result = $response->json();

            // Struktur response Komerce biasanya: { meta: {...}, data: [...] }
            // Namun wrapper RajaOngkir kadang mempertahankan struktur lama { rajaongkir: { results: ... } }
            // Kita handle kedua kemungkinan agar aman.

            $costs = [];
            if (isset($result['data'][0]['costs'])) {
                // Struktur Baru Komerce Murni
                $costs = $result['data'][0]['costs'];
            } elseif (isset($result['rajaongkir']['results'][0]['costs'])) {
                // Struktur Wrapper Legacy
                $costs = $result['rajaongkir']['results'][0]['costs'];
            }

            return [
                'status' => true,
                'data' => $costs
            ];

        } catch (\Exception $e) {
            Log::error('RajaOngkir Service Exception: ' . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Mendapatkan daftar Provinsi
     * Endpoint: /destination/province
     */
    public function getProvinces()
    {
        return $this->fetchData('/destination/province');
    }

    /**
     * Mendapatkan daftar Kota
     * Endpoint: /destination/city
     * @param int|null $provinceId Filter berdasarkan provinsi
     */
    public function getCities($provinceId = null)
    {
        $params = $provinceId ? ['province' => $provinceId] : [];
        return $this->fetchData('/destination/city', $params);
    }

    /**
     * Mendapatkan daftar Kecamatan (District)
     * Endpoint Komerce Baru: /destination/district/{city_id}
     * Berbeda dengan versi lama yang pakai query param ?city=
     */
    public function getSubdistricts($cityId)
    {
        // Perhatikan format URL yang menggunakan Path Parameter
        return $this->fetchData("/destination/district/{$cityId}");
    }

    /**
     * Helper internal untuk fetch data
     */
    private function fetchData($endpoint, $params = [])
    {
        try {
            $cred = $this->getCredential($this->providerCode);
            $baseUrl = $cred->config['base_url'] ?? 'https://rajaongkir.komerce.id/api/v1';

            $response = Http::withHeaders([
                'key' => $cred->public_k,
            ])->get("{$baseUrl}{$endpoint}", $params);

            if ($response->failed()) {
                Log::error("RajaOngkir Fetch Error ({$endpoint})", $response->json());
                return [];
            }

            $result = $response->json();

            // Handle struktur data response Komerce
            // Biasanya ada di $result['data'] atau $result['rajaongkir']['results']
            return $result['data'] ?? $result['rajaongkir']['results'] ?? [];

        } catch (\Exception $e) {
            Log::error("RajaOngkir Exception ({$endpoint}): " . $e->getMessage());
            return [];
        }
    }
}
