<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService extends BaseIntegrationService
{
    protected string $providerCode = 'gemini';

    /**
     * Generate konten teks menggunakan Google Gemini
     * Cocok untuk membuat deskripsi produk otomatis
     */
    public function generateContent(string $prompt)
    {
        try {
            $cred = $this->getCredential($this->providerCode);

            // API Key Gemini ada di 'public_k' atau 'encrypted_k' (tergantung Anda simpan dimana)
            // Asumsi di public_k untuk kemudahan akses
            $apiKey = $cred->public_k;

            // Endpoint Gemini Pro
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key={$apiKey}";

            $response = Http::post($url, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ]
            ]);

            if ($response->failed()) {
                Log::error('Gemini API Error', $response->json());
                return "Maaf, AI sedang sibuk.";
            }

            $result = $response->json();

            // Ambil text dari response struktur Gemini
            return $result['candidates'][0]['content']['parts'][0]['text'] ?? '';

        } catch (\Exception $e) {
            Log::error('Gemini Service Exception: ' . $e->getMessage());
            return "Fitur AI belum tersedia.";
        }
    }
}
