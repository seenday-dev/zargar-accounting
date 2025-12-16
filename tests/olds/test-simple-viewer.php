<?php
/**
 * Test the new simple logs viewer system
 */

// Simulate WordPress environment
define('ABSPATH', __DIR__ . '/');
define('ZARGAR_ACCOUNTING_PLUGIN_DIR', __DIR__ . '/');

// Load Composer
require_once __DIR__ . '/vendor/autoload.php';

// Mock WordPress functions
if (!function_exists('sanitize_text_field')) {
    function sanitize_text_field($str) {
        return htmlspecialchars(strip_tags($str), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('admin_url')) {
    function admin_url($path) {
        return 'http://localhost/wp-admin/' . $path;
    }
}

if (!function_exists('esc_html')) {
    function esc_html($text) {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
}

// Import classes
use ZargarAccounting\Admin\LogsViewer;
use ZargarAccounting\Logger\MonologManager;

echo "=== Testing New Simple Logs Viewer System ===\n\n";

// Test 1: Create viewer instance
echo "1. Creating LogsViewer instance...\n";
$viewer = LogsViewer::getInstance();
echo "   ✅ Instance created\n\n";

// Test 2: Test formatLevel method
echo "2. Testing formatLevel() method...\n";
$levels = ['INFO', 'DEBUG', 'WARNING', 'ERROR', 'CRITICAL'];
foreach ($levels as $level) {
    $formatted = $viewer->formatLevel($level);
    echo "   $level: " . (strlen($formatted) > 0 ? '✅' : '❌') . "\n";
}
echo "\n";

// Test 3: Test formatContext method
echo "3. Testing formatContext() method...\n";
$contexts = [
    '',
    '{"product_id": 123, "name": "Gold Ring"}',
    'Simple text context'
];
foreach ($contexts as $ctx) {
    $formatted = $viewer->formatContext($ctx);
    $label = empty($ctx) ? '(empty)' : (strlen($ctx) > 20 ? substr($ctx, 0, 20) . '...' : $ctx);
    echo "   $label: " . (strlen($formatted) > 0 ? '✅' : '❌') . "\n";
}
echo "\n";

// Test 4: Check if logs exist
echo "4. Checking existing logs...\n";
$logger = MonologManager::getInstance();
$channels = ['product', 'sales', 'price', 'error'];
foreach ($channels as $channel) {
    $stats = $logger->getStats($channel);
    echo "   $channel: {$stats['total']} logs\n";
}
echo "\n";

// Test 5: Simulate rendering (without actual output)
echo "5. Testing render logic (without HTML output)...\n";

// Simulate URL parameter
$_GET['channel'] = 'product';

try {
    // Get data like render() would
    $current_channel = isset($_GET['channel']) ? sanitize_text_field($_GET['channel']) : 'product';
    
    $valid_channels = [
        'product' => '📦 محصولات',
        'sales' => '💰 فروش',
        'price' => '💵 قیمت',
        'error' => '⚠️ خطاها'
    ];
    
    if (!isset($valid_channels[$current_channel])) {
        $current_channel = 'product';
    }
    
    $logs = $logger->getRecentLogs($current_channel, 10);
    
    $stats = [];
    foreach (array_keys($valid_channels) as $channel) {
        $stats[$channel] = $logger->getStats($channel);
    }
    
    echo "   ✅ Channel: $current_channel\n";
    echo "   ✅ Logs fetched: " . count($logs) . "\n";
    echo "   ✅ Stats fetched: " . count($stats) . " channels\n";
    echo "\n";
    
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n\n";
}

// Test 6: Verify template file exists
echo "6. Checking template file...\n";
$template_path = __DIR__ . '/templates/admin/simple-logs.php';
if (file_exists($template_path)) {
    echo "   ✅ Template exists: simple-logs.php\n";
    $size = filesize($template_path);
    echo "   ✅ Template size: $size bytes\n";
} else {
    echo "   ❌ Template not found!\n";
}
echo "\n";

echo "=== All Tests Complete ===\n";
echo "\nNext steps:\n";
echo "1. Upload updated files to WordPress\n";
echo "2. Clear WordPress cache\n";
echo "3. Visit: wp-admin/admin.php?page=zargar-accounting-logs\n";
echo "4. Logs should load instantly without AJAX\n";
echo "5. Click tabs to switch between log types\n";
