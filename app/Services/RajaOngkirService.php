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
            // URL dari dokumentasi Komerce / User Snippet
            $baseUrl = $cred->config['base_url'] ?? 'https://rajaongkir.komerce.id/api/v1';
            
            // Endpoint: /calculate/domestic-cost (sesuai snippet user)
            // Content-Type: application/x-www-form-urlencoded
            $response = Http::asForm()->withHeaders([
                'key' => $cred->public_k,
            ])->post("{$baseUrl}/calculate/domestic-cost", [
                'origin' => $origin,        
                'destination' => $destination, 
                'weight' => $weight,
                'courier' => $courier
            ]);

            if ($response->failed()) {
                // Jika error 400-499 (misal 404 rute tidak ada), anggap sukses tapi data kosong
                if ($response->clientError()) {
                    return ['status' => true, 'data' => []];
                }

                Log::error("RajaOngkir Error: " . $response->body());
                return ['status' => false, 'message' => 'Gagal koneksi API Ongkir.'];
            }

            $result = $response->json();

            // Struktur Komerce: { meta: {...}, data: [ ... ] }
            // API ini mengembalikan data langsung dalam array
            $data = $result['data'] ?? [];

            return [
                'status' => true,
                'data' => $data
            ];
            
        } catch (\Exception $e) {
            Log::error('RajaOngkir Exception: ' . $e->getMessage());
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
