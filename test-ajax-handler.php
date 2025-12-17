<?php
/**
 * Test AJAX Handler Registration
 * 
 * این فایل تست می‌کند که آیا AJAX handlers درست register شده‌اند یا نه
 */

// Load WordPress
require_once '/var/www/html/wordpress/wp-load.php';

echo "🔍 بررسی AJAX Handlers برای Zargar Accounting\n";
echo str_repeat('=', 60) . "\n\n";

// Check if plugin is active
if (!defined('ZARGAR_ACCOUNTING_VERSION')) {
    echo "❌ پلاگین Zargar Accounting فعال نیست!\n";
    exit(1);
}

echo "✅ پلاگین فعال است (نسخه: " . ZARGAR_ACCOUNTING_VERSION . ")\n\n";

// Check class existence
$classes = [
    'ZargarAccounting\Admin\ProductImportManager',
    'ZargarAccounting\Admin\MenuManager',
    'ZargarAccounting\Admin\AssetsManager',
    'ZargarAccounting\Logger\MonologManager',
];

echo "📦 بررسی کلاس‌ها:\n";
foreach ($classes as $class) {
    $exists = class_exists($class);
    echo ($exists ? "✅" : "❌") . " {$class}\n";
}
echo "\n";

// Check ProductImportManager instance
echo "🔧 بررسی ProductImportManager:\n";
try {
    $manager = \ZargarAccounting\Admin\ProductImportManager::getInstance();
    echo "✅ Instance ایجاد شد\n";
    
    // Check if hooks are registered
    echo "\n📌 بررسی AJAX Actions:\n";
    
    $actions = [
        'zargar_get_import_stats',
        'zargar_start_import',
        'zargar_get_import_progress',
        'zargar_clear_import_logs',
        'zargar_search_products',
        'zargar_import_specific_products'
    ];
    
    foreach ($actions as $action) {
        $hasAction = has_action("wp_ajax_{$action}");
        echo ($hasAction ? "✅" : "❌") . " wp_ajax_{$action}";
        
        if ($hasAction) {
            echo " (registered)\n";
        } else {
            echo " (NOT registered)\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ خطا: " . $e->getMessage() . "\n";
}

echo "\n";
echo str_repeat('=', 60) . "\n";

// Simulate AJAX request
echo "\n🧪 شبیه‌سازی AJAX Request:\n";
echo str_repeat('-', 60) . "\n";

// Set up fake AJAX environment
$_POST['action'] = 'zargar_search_products';
$_POST['codes'] = '["GD01000316"]';
$_POST['nonce'] = wp_create_nonce('zargar_import_nonce');
$_REQUEST['_ajax_nonce'] = $_POST['nonce'];

// Set current user to admin
wp_set_current_user(1);

echo "📝 POST Data:\n";
echo "   action: {$_POST['action']}\n";
echo "   codes: {$_POST['codes']}\n";
echo "   nonce: " . substr($_POST['nonce'], 0, 20) . "...\n";
echo "   user_id: " . get_current_user_id() . "\n\n";

// Try to call the handler
echo "🚀 فراخوانی handler...\n\n";

try {
    ob_start();
    
    // Call the action
    do_action('wp_ajax_zargar_search_products');
    
    $output = ob_get_clean();
    
    if (!empty($output)) {
        echo "📤 خروجی:\n";
        echo $output . "\n";
        
        // Try to decode as JSON
        $json = json_decode($output, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            echo "\n✅ پاسخ JSON معتبر است:\n";
            echo "   success: " . ($json['success'] ? 'true' : 'false') . "\n";
            if (isset($json['data'])) {
                if (is_array($json['data'])) {
                    echo "   data: " . json_encode($json['data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
                } else {
                    echo "   data: {$json['data']}\n";
                }
            }
        }
    } else {
        echo "⚠️  هیچ خروجی از handler دریافت نشد\n";
        echo "   این می‌تواند به معنای عدم ثبت handler باشد\n";
    }
    
} catch (Exception $e) {
    echo "❌ خطا در اجرای handler:\n";
    echo "   " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat('=', 60) . "\n";
echo "✨ تست تکمیل شد\n";
