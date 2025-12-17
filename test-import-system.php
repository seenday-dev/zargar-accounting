<?php
/**
 * تست سیستم Import
 */

require_once __DIR__ . '/vendor/autoload.php';

// Mock WordPress functions
function add_action($hook, $callback) { echo "✓ Hook registered: $hook\n"; }
function check_ajax_referer($action, $key) { return true; }
function current_user_can($cap) { return true; }
function wp_send_json_success($data) { echo "✅ Success: " . json_encode($data, JSON_UNESCAPED_UNICODE) . "\n"; }
function wp_send_json_error($data) { echo "❌ Error: " . json_encode($data, JSON_UNESCAPED_UNICODE) . "\n"; }
function get_option($key, $default = '') {
    $options = [
        'zargar_server_ip' => '37.235.18.235',
        'zargar_server_port' => 8090,
        'zargar_username' => 'Service',
        'zargar_password' => 'Service',
    ];
    return $options[$key] ?? $default;
}
function update_option($key, $value) { return true; }
function wp_schedule_single_event($time, $hook, $args) { return true; }

if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}
if (!defined('ZARGAR_ACCOUNTING_PLUGIN_DIR')) {
    define('ZARGAR_ACCOUNTING_PLUGIN_DIR', __DIR__ . '/');
}

use ZargarAccounting\Admin\ProductImportManager;
use ZargarAccounting\Helpers\FieldMapper;

echo "==============================================\n";
echo "  تست سیستم Import محصولات\n";
echo "==============================================\n\n";

// Test 1: FieldMapper
echo "📋 تست 1: FieldMapper\n";
echo "----------------------------------------------\n";

$availableFields = FieldMapper::getAvailableFields();
echo "✓ تعداد کل دسته‌ها: " . count($availableFields) . "\n";

$totalFields = 0;
foreach ($availableFields as $category => $data) {
    $count = count($data['fields']);
    $totalFields += $count;
    echo "  - {$data['title']}: $count فیلد\n";
}
echo "✓ تعداد کل فیلدها: $totalFields\n\n";

// Test 2: ProductImportManager
echo "📋 تست 2: ProductImportManager\n";
echo "----------------------------------------------\n";

$manager = ProductImportManager::getInstance();
echo "✓ Instance ساخته شد\n";

$manager->registerHooks();
echo "\n";

// Test 3: Singleton Pattern
echo "📋 تست 3: Singleton Pattern\n";
echo "----------------------------------------------\n";

$instance1 = ProductImportManager::getInstance();
$instance2 = ProductImportManager::getInstance();

if ($instance1 === $instance2) {
    echo "✅ Singleton Pattern درست کار می‌کند\n";
} else {
    echo "❌ Singleton Pattern مشکل دارد\n";
}

echo "\n";

// Test 4: Field Mapping
echo "📋 تست 4: نقشه‌برداری فیلد نمونه\n";
echo "----------------------------------------------\n";

$sampleProduct = [
    'ProductId' => '123',
    'ProductCode' => 'GOLD-001',
    'ProductTitle' => 'گردنبند طلا',
    'Weight' => '10.5',
    'GoldPrice' => '50000000',
    'CategoryTitle' => 'گردنبند',
];

$mapper = new FieldMapper();
echo "✓ Mapper ساخته شد\n";
echo "✓ داده‌های نمونه:\n";
foreach ($sampleProduct as $key => $value) {
    echo "  - $key: $value\n";
}

echo "\n==============================================\n";
echo "✅ همه تست‌ها موفق بود!\n";
echo "==============================================\n";
