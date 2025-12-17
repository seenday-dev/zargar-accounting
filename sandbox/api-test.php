<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

$config = [
    'host' => '37.235.18.235',
    'port' => 8090,
    'username' => 'Service',
    'password' => 'Service',
];

$pages = import_madegold_products($config, 100);

if (empty($pages)) {
    echo "هیچ محصولی دریافت نشد.\n";
    exit;
}

echo "تعداد صفحات دریافت‌شده: " . count($pages) . "\n";
echo "نمونه خروجی:\n";
echo json_encode($pages[0], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n";

function import_madegold_products(array $config, int $pageCount): array {
    $client = new Client([
        'base_uri' => sprintf('http://%s:%d', $config['host'], $config['port']),
        'timeout' => 20,
        'http_errors' => false,
        'headers' => ['Accept' => 'application/json'],
    ]);

    $token = null;
    try {
        $response = $client->get('/services/login/', [
            'query' => [
                'username' => $config['username'],
                'password' => $config['password'],
            ],
        ]);
        $body = (string) $response->getBody();
        $login = json_decode($body, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "Login response invalid: {$body}\n";
            return [];
        }
        $token = $login['Message']['UserKey'] ?? $login['Result']['UserKey'] ?? null;
    } catch (GuzzleException $e) {
        echo "خطا در ورود: {$e->getMessage()}\n";
        return [];
    }

    if ($token === null) {
        echo "UserKey یافت نشد.\n";
        return [];
    }

    $pages = [];
    $pageIndex = 1;

    while (true) {
        try {
            $response = $client->get('/Services/MadeGold/List/', [
                'query' => [
                    'userkey' => $token,
                    'PageIndex' => $pageIndex,
                    'PageCount' => $pageCount,
                ],
            ]);
        } catch (GuzzleException $e) {
            echo "دریافت صفحه {$pageIndex} با خطا روبه‌رو شد: {$e->getMessage()}\n";
            break;
        }

        $decoded = json_decode((string) $response->getBody(), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "خروجی صفحه {$pageIndex} معتبر نیست.\n";
            break;
        }

        if (($decoded['Status'] ?? null) === 'Error' && ($decoded['Message'] ?? '') === 'خطا در خروجی خالی') {
            echo "صفحات تمام شد.\n";
            break;
        }

        if (!isset($decoded['Result']) || !is_array($decoded['Result'])) {
            $pages[] = $decoded;
            $pageIndex++;
            continue;
        }

        foreach ($decoded['Result'] as &$product) {
            $imageFields = ['ImageURL1','ImageURL2','ImageURL3','ImageURL4','ImageURL5','ImageURL6','DefaultImageURL'];
            $baseUri = (string) $client->getConfig('base_uri');
            foreach ($imageFields as $field) {
                if (!empty($product[$field])) {
                    $product[$field] = rtrim($baseUri, '/') . '/' . ltrim((string) $product[$field], '/');
                }
            }

            $gold = (float) ($product['GoldPrice'] ?? 0);
            $wage = (float) ($product['WageOfPrice'] ?? 0);
            $product['WagePercent'] = $gold > 0 ? round(($wage / $gold) * 100, 4) : 0.0;
        }
        unset($product);

        $pages[] = $decoded;
        $pageIndex++;
    }

    return $pages;
}
