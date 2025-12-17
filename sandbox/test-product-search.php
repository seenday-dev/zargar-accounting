<?php
/**
 * تست جستجوی محصول با کد
 */

require_once __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client;

$config = [
    'host' => '37.235.18.235',
    'port' => 8090,
    'username' => 'Service',
    'password' => 'Service',
];

// Login
$client = new Client([
    'base_uri' => sprintf('http://%s:%d', $config['host'], $config['port']),
    'timeout' => 20,
    'verify' => false,
    'http_errors' => false,
]);

echo "🔐 در حال ورود به سیستم...\n";
$response = $client->get('/services/login/', [
    'query' => [
        'username' => $config['username'],
        'password' => $config['password'],
    ],
]);

$login = json_decode($response->getBody(), true);
$userkey = $login['Message']['UserKey'] ?? $login['Result']['UserKey'] ?? null;

if (!$userkey) {
    die("❌ خطا در دریافت UserKey\n");
}

echo "✅ ورود موفق - UserKey: {$userkey}\n\n";

// تست جستجوی محصولات با کدهای مختلف
$testCodes = ['GD01000316', 'GD01000315', 'GD01000377'];

echo "🔍 تست جستجوی محصولات...\n";
echo str_repeat("=", 60) . "\n";

foreach ($testCodes as $code) {
    echo "\n📦 جستجوی کد: {$code}\n";
    echo str_repeat("-", 60) . "\n";
    
    // روش 1: جستجو در لیست کامل
    try {
        $response = $client->get('/Services/MadeGold/List/', [
            'query' => [
                'userkey' => $userkey,
                'PageIndex' => 1,
                'PageCount' => 100,
            ],
        ]);
        
        $result = json_decode($response->getBody(), true);
        
        if (isset($result['Result']) && is_array($result['Result'])) {
            $found = false;
            foreach ($result['Result'] as $product) {
                if (isset($product['ProductCode']) && $product['ProductCode'] === $code) {
                    echo "✅ محصول یافت شد در لیست:\n";
                    echo "   - ID: {$product['ProductId']}\n";
                    echo "   - Code: {$product['ProductCode']}\n";
                    echo "   - Title: {$product['ProductTitle']}\n";
                    echo "   - Category: " . ($product['CategoryTitle'] ?? 'N/A') . "\n";
                    echo "   - Weight: " . ($product['Weight'] ?? 'N/A') . "\n";
                    echo "   - Price: " . ($product['GoldPrice'] ?? 'N/A') . "\n";
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                echo "❌ محصول در صفحه اول یافت نشد (شاید در صفحات بعدی باشد)\n";
            }
        }
    } catch (Exception $e) {
        echo "❌ خطا: " . $e->getMessage() . "\n";
    }
    
    // روش 2: تست endpoint های مختلف
    $endpoints = [
        '/services/product/getProductItemsByCode',
        '/services/product/getByCode',
        '/Services/Product/GetByCode',
        '/Services/MadeGold/GetByCode',
    ];
    
    echo "\n🔎 تست endpoint های مختلف:\n";
    foreach ($endpoints as $endpoint) {
        try {
            $response = $client->post($endpoint, [
                'json' => [
                    'UserKey' => $userkey,
                    'Code' => $code,
                    'ProductCode' => $code,
                ]
            ]);
            
            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();
            
            echo "   {$endpoint}:\n";
            echo "     Status: {$statusCode}\n";
            
            $data = json_decode($body, true);
            if ($data && isset($data['Result'])) {
                echo "     ✅ پاسخ دارد: " . substr(json_encode($data['Result'], JSON_UNESCAPED_UNICODE), 0, 100) . "...\n";
            } elseif ($data && isset($data['data'])) {
                echo "     ✅ پاسخ دارد: " . substr(json_encode($data['data'], JSON_UNESCAPED_UNICODE), 0, 100) . "...\n";
            } else {
                echo "     ❌ پاسخ خالی یا نامعتبر\n";
            }
        } catch (Exception $e) {
            echo "   {$endpoint}: ❌ {$e->getMessage()}\n";
        }
    }
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "✅ تست کامل شد\n";
