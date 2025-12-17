<?php
/**
 * تست جستجوی محصولات با کد - روش صحیح
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
    'timeout' => 30,
    'verify' => false,
    'http_errors' => false,
]);

echo "🔐 در حال ورود...\n";
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

echo "✅ UserKey: {$userkey}\n\n";

// کدهای جستجو
$searchCodes = ['GD01000316', 'GD01000315'];
$searchCodes = array_map('strtoupper', array_map('trim', $searchCodes));

echo "🔍 جستجوی کدها: " . implode(', ', $searchCodes) . "\n";
echo str_repeat("=", 70) . "\n\n";

$foundProducts = [];
$found = [];
$pageIndex = 1;
$maxPages = 10;

while ($pageIndex <= $maxPages && count($found) < count($searchCodes)) {
    echo "📄 صفحه {$pageIndex}... ";
    
    try {
        $response = $client->get('/Services/MadeGold/List/', [
            'query' => [
                'userkey' => $userkey,
                'PageIndex' => $pageIndex,
                'PageCount' => 100
            ]
        ]);
        
        $result = json_decode($response->getBody()->getContents(), true);
        
        if (isset($result['Status']) && $result['Status'] === 'Error') {
            echo "❌ خطا یا پایان صفحات\n";
            break;
        }
        
        if (!isset($result['Result']) || !is_array($result['Result']) || empty($result['Result'])) {
            echo "❌ پایان محصولات\n";
            break;
        }
        
        $pageCount = count($result['Result']);
        echo "{$pageCount} محصول";
        
        // جستجو در این صفحه
        foreach ($result['Result'] as $product) {
            $productCode = strtoupper(trim($product['ProductCode'] ?? ''));
            
            if (in_array($productCode, $searchCodes) && !isset($found[$productCode])) {
                $foundProducts[] = $product;
                $found[$productCode] = true;
                echo " ✅ {$productCode} یافت شد!";
            }
        }
        
        echo "\n";
        
        if (count($found) >= count($searchCodes)) {
            echo "\n🎉 همه محصولات یافت شدند!\n";
            break;
        }
        
        $pageIndex++;
        
    } catch (Exception $e) {
        echo "❌ خطا: " . $e->getMessage() . "\n";
        break;
    }
}

echo "\n" . str_repeat("=", 70) . "\n";
echo "📊 نتیجه:\n";
echo "   - تعداد کدهای جستجو شده: " . count($searchCodes) . "\n";
echo "   - تعداد محصولات یافت شده: " . count($foundProducts) . "\n";
echo "   - تعداد صفحات بررسی شده: " . ($pageIndex - 1) . "\n\n";

if (!empty($foundProducts)) {
    echo "📦 محصولات یافت شده:\n";
    echo str_repeat("-", 70) . "\n";
    
    foreach ($foundProducts as $product) {
        echo "\n✓ کد: {$product['ProductCode']}\n";
        echo "  عنوان: {$product['ProductTitle']}\n";
        echo "  دسته: " . ($product['CategoryTitle'] ?? 'N/A') . "\n";
        echo "  وزن: " . ($product['Weight'] ?? 'N/A') . " گرم\n";
        echo "  قیمت: " . number_format($product['GoldPrice'] ?? 0) . " ریال\n";
    }
} else {
    echo "❌ هیچ محصولی یافت نشد\n";
    echo "\n💡 نکات:\n";
    echo "   - کدها را با حروف بزرگ وارد کنید (مثل GD01000316)\n";
    echo "   - مطمئن شوید کد محصول در سیستم وجود دارد\n";
    echo "   - شاید محصولات در صفحات بعدی باشند (maxPages را افزایش دهید)\n";
}

echo "\n" . str_repeat("=", 70) . "\n";
