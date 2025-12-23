<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/*
|--------------------------------------------------------------------------
| GEMINI AI SERVICE
|--------------------------------------------------------------------------
*/
class GeminiService extends BaseIntegrationService
{
    protected string $providerCode = 'gemini';

    /*
    |--------------------------------------------------------------------------
    | GENERATE CONTENT
    |--------------------------------------------------------------------------
    */
    public function generateContent(string $prompt): string
    {
        try {
            $cred   = $this->getCredential($this->providerCode);
            $apiKey = $cred->public_k;

            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key={$apiKey}";

            $response = Http::post($url, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt],
                        ],
                    ],
                ],
            ]);

            if ($response->failed()) {
                Log::error('Gemini API Error', $response->json());
                return 'Maaf, AI sedang sibuk.';
            }

            return $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? '';

        } catch (\Exception $e) {
            Log::error('Gemini Service Exception: ' . $e->getMessage());
            return 'Fitur AI belum tersedia.';
        }
    }
}
