<?php
require 'vendor/autoload.php';

use Illuminate\Support\Facades\Http;

$key = 'd5LxeDvW8f8033e2b179a590DEyT4Xf1';
$baseUrl = 'https://rajaongkir.komerce.id/api/v1';

// TESTING SENDIRI
// Endpoint: /calculate/domestic-cost
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "{$baseUrl}/calculate/domestic-cost");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["key: $key", "Content-Type: application/x-www-form-urlencoded"]);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'origin' => 1391, 
    'destination' => 31397,
    'weight' => 1000,
    'courier' => 'jne'
]));
$result = curl_exec($ch);
echo "Cost Test Result: " . substr($result, 0, 500) . "\n";
curl_close($ch);
