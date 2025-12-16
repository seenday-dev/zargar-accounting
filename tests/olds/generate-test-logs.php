<?php
/**
 * Generate Test Logs
 * 
 * Run this to generate sample logs for testing
 */

require_once __DIR__ . '/vendor/autoload.php';

define('ZARGAR_ACCOUNTING_PLUGIN_DIR', __DIR__ . '/');
define('ABSPATH', '/tmp/');

// Mock WordPress functions
function wp_mkdir_p($target) { return @mkdir($target, 0755, true); }
function get_current_user_id() { return 1; }
function get_userdata($id) { return (object)['user_login' => 'admin']; }

use ZargarAccounting\Logger\MonologManager;

echo "🔧 Generating test logs...\n\n";

$logger = MonologManager::getInstance();

// Generate product logs
echo "📦 Creating product logs...\n";
for ($i = 1; $i <= 5; $i++) {
    $logger->product("محصول شماره {$i} اضافه شد", [
        'product_id' => 100 + $i,
        'name' => "محصول تست {$i}",
        'price' => 100000 * $i
    ]);
}

// Generate sales logs
echo "💰 Creating sales logs...\n";
for ($i = 1; $i <= 5; $i++) {
    $logger->sales("سفارش شماره {$i} ثبت شد", [
        'order_id' => 200 + $i,
        'customer' => "مشتری {$i}",
        'total' => 500000 * $i
    ]);
}

// Generate price logs
echo "💵 Creating price logs...\n";
for ($i = 1; $i <= 5; $i++) {
    $logger->price("قیمت محصول {$i} بروز شد", [
        'product_id' => 100 + $i,
        'old_price' => 100000,
        'new_price' => 120000
    ]);
}

// Generate error logs
echo "⚠️ Creating error logs...\n";
for ($i = 1; $i <= 5; $i++) {
    $logger->error("خطای شماره {$i} رخ داد", [
        'error_code' => 500 + $i,
        'message' => "خطای تست شماره {$i}"
    ]);
}

// Generate general logs
echo "📝 Creating general logs...\n";
$logger->info('سیستم شروع به کار کرد');
$logger->warning('حافظه در حال پر شدن است');
$logger->debug('متغیر تست', ['value' => 123]);

echo "\n✅ Test logs generated successfully!\n\n";

// Show stats
echo "📊 Statistics:\n";
$channels = ['product', 'sales', 'price', 'error', 'general'];
foreach ($channels as $channel) {
    $stats = $logger->getStats($channel);
    echo "  {$channel}: {$stats['total']} logs ({$stats['size']} bytes)\n";
}

echo "\n✅ Done!\n";
