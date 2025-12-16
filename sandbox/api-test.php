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

function requestProductUserKey(array $config): ?string {
    $client = new Client([
        'base_uri' => sprintf('http://%s:%d', $config['host'], $config['port']),
        'timeout' => 20,
        'http_errors' => false,
        'headers' => ['Accept' => 'application/json'],
    ]);

    try {
        $response = $client->get('/services/login/', [
            'query' => [
                'username' => $config['username'],
                'password' => $config['password'],
            ],
        ]);
    } catch (GuzzleException) {
        return null;
    }

    $decoded = json_decode((string) $response->getBody(), true);
    return json_last_error() === JSON_ERROR_NONE
        ? ($decoded['Message']['UserKey'] ?? $decoded['Result']['UserKey'] ?? null)
        : null;
}

$token = requestProductUserKey($config);
if ($token === null) {
    echo "UserKey not found\n";
    exit(1);
}

echo $token . PHP_EOL;

function fetchMadeGoldProducts(array $config, string $userKey, int $pageCount = 100, array $fieldsToRemove = []): array {
    $pages = [];
    $pageIndex = 1;
    $client = new Client([
        'base_uri' => sprintf('http://%s:%d', $config['host'], $config['port']),
        'timeout' => 20,
        'http_errors' => false,
        'headers' => ['Accept' => 'application/json'],
    ]);

    while (true) {
        try {
            $response = $client->get('/Services/MadeGold/List/', [
                'query' => [
                    'userkey' => $userKey,
                    'PageIndex' => $pageIndex,
                    'PageCount' => $pageCount,
                ],
            ]);
        } catch (GuzzleException) {
            echo "MadeGold request failed on page {$pageIndex}.\n";
            break;
        }

        $decoded = json_decode((string) $response->getBody(), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "Invalid JSON on page {$pageIndex}.\n";
            break;
        }

        $imageFields = [
            'ImageURL1',
            'ImageURL2',
            'ImageURL3',
            'ImageURL4',
            'ImageURL5',
            'ImageURL6',
            'DefaultImageURL',
        ];
        $baseUrl = sprintf('http://%s:%d', $config['host'], $config['port']);

        $status = $decoded['Status'] ?? null;
        $message = $decoded['Message'] ?? null;

        if ($status === 'Error' && $message === 'خطا در خروجی خالی') {
            echo "Reached empty response at page {$pageIndex}.\n";
            break;
        }

        if (isset($decoded['Result']) && is_array($decoded['Result'])) {
            foreach ($decoded['Result'] as &$product) {
                foreach ($imageFields as $field) {
                    if (!empty($product[$field])) {
                        $product[$field] = rtrim($baseUrl, '/') . '/' . ltrim($product[$field], '/');
                    }
                }
                $goldPrice = (float) ($product['GoldPrice'] ?? 0);
                $wagePrice = (float) ($product['WageOfPrice'] ?? 0);
                $product['WagePercent'] = $goldPrice > 0 ? round(($wagePrice / $goldPrice) * 100, 4) : 0.0;
            }
            unset($product);
        }

        if (!empty($fieldsToRemove) && isset($decoded['Result']) && is_array($decoded['Result'])) {
            foreach ($decoded['Result'] as &$product) {
                foreach ($fieldsToRemove as $field) {
                    unset($product[$field]);
                }
            }
            unset($product);
        }

        echo json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL;
        $pages[] = $decoded;
        $pageIndex++;
    }

    return $pages;
}

$allProducts = fetchMadeGoldProducts($config, $token, 100, [
    'DesignerCode',
    'OfficeCode',
    'OldCode',
    'SizeTitle',
    'WageOfPrice',
    'ImageURL2',
    'ImageURL3',
    'ImageURL4',
    'ImageURL5',
    'ImageURL6',
    'other1',
    'other2',
]);



function saveProductsToFile(array $data, string $path): bool {
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($json === false) {
        return false;
    }
    return file_put_contents($path, $json) !== false;
}

if (!saveProductsToFile($allProducts, __DIR__ . '/madegold-products.json')) {
    echo "Failed to save products to file.\n";
}
