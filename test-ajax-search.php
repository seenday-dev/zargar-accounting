<?php
/**
 * Test AJAX Search Handler
 * این فایل برای تست مستقیم handler جستجوی محصولات است
 */

// Load WordPress
require_once __DIR__ . '/../../../wp-load.php';

// Check if we can access WordPress
if (!defined('ABSPATH')) {
    die('WordPress loaded failed');
}

echo "=== Test AJAX Search Handler ===\n\n";

// Check if user is logged in
if (!is_user_logged_in()) {
    echo "❌ کاربر لاگین نیست\n";
    echo "لطفاً ابتدا به وردپرس لاگین کنید\n\n";
    exit;
}

echo "✓ کاربر لاگین است: " . wp_get_current_user()->user_login . "\n";
echo "✓ سطح دسترسی: " . (current_user_can('manage_options') ? 'مدیر' : 'عادی') . "\n\n";

// Check if ProductImportManager is loaded
if (!class_exists('ZargarAccounting\Admin\ProductImportManager')) {
    echo "❌ کلاس ProductImportManager یافت نشد\n\n";
    exit;
}

echo "✓ کلاس ProductImportManager یافت شد\n\n";

// Check if AJAX action is registered
$registered = has_action('wp_ajax_zargar_search_products');
if ($registered) {
    echo "✓ Action 'wp_ajax_zargar_search_products' ثبت شده است\n";
} else {
    echo "❌ Action 'wp_ajax_zargar_search_products' ثبت نشده است\n";
}

// List all registered zargar actions
echo "\n=== تمام اکشن‌های AJAX زرگر ===\n";
global $wp_filter;
foreach ($wp_filter as $tag => $callbacks) {
    if (strpos($tag, 'zargar') !== false) {
        echo "- $tag\n";
    }
}

echo "\n=== تست دستی handler ===\n";

// Simulate AJAX request
$_POST['nonce'] = wp_create_nonce('zargar_import_nonce');
$_POST['codes'] = json_encode(['GD01000315']);

echo "Codes: " . $_POST['codes'] . "\n";
echo "Nonce: " . $_POST['nonce'] . "\n\n";

// Call the handler directly
try {
    $manager = \ZargarAccounting\Admin\ProductImportManager::getInstance();
    
    echo "شروع تست handler...\n\n";
    
    // Capture output
    ob_start();
    $manager->ajaxSearchProducts();
    $output = ob_get_clean();
    
    echo "خروجی handler:\n";
    echo $output . "\n";
    
} catch (Exception $e) {
    echo "❌ خطا در اجرای handler: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== پایان تست ===\n";
