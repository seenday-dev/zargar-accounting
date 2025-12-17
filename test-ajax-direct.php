<?php
/**
 * Direct Test for AJAX Search
 * این فایل را در ریشه وردپرس قرار دهید و از مرورگر اجرا کنید
 */

// Load WordPress
define('WP_USE_THEMES', false);
require_once('./wp-load.php');

// Set headers
header('Content-Type: application/json; charset=utf-8');

// Enable error display
error_reporting(E_ALL);
ini_set('display_errors', '1');

echo json_encode([
    'step' => 'WordPress loaded',
    'wp_version' => get_bloginfo('version'),
    'php_version' => PHP_VERSION
]) . "\n\n";

// Check if user is logged in
if (!is_user_logged_in()) {
    echo json_encode([
        'error' => 'کاربر لاگین نیست',
        'message' => 'لطفاً ابتدا به وردپرس لاگین کنید'
    ]);
    exit;
}

echo json_encode([
    'step' => 'User logged in',
    'user' => wp_get_current_user()->user_login,
    'can_manage' => current_user_can('manage_options')
]) . "\n\n";

// Check if class exists
if (!class_exists('ZargarAccounting\Admin\ProductImportManager')) {
    echo json_encode([
        'error' => 'Class not found',
        'message' => 'کلاس ProductImportManager یافت نشد'
    ]);
    exit;
}

echo json_encode([
    'step' => 'Class exists',
    'class' => 'ZargarAccounting\Admin\ProductImportManager'
]) . "\n\n";

// Simulate POST data
$_POST['nonce'] = wp_create_nonce('zargar_import_nonce');
$_POST['codes'] = json_encode(['GD01000315']);
$_POST['action'] = 'zargar_search_products';

echo json_encode([
    'step' => 'POST data set',
    'nonce' => $_POST['nonce'],
    'codes' => $_POST['codes']
]) . "\n\n";

// Try to call the handler
try {
    echo json_encode(['step' => 'Getting instance...']) . "\n\n";
    
    $manager = \ZargarAccounting\Admin\ProductImportManager::getInstance();
    
    echo json_encode(['step' => 'Instance created']) . "\n\n";
    
    // Capture output
    ob_start();
    
    echo json_encode(['step' => 'Calling ajaxSearchProducts...']) . "\n\n";
    
    $manager->ajaxSearchProducts();
    
    $output = ob_get_clean();
    
    echo json_encode([
        'step' => 'Method called',
        'output' => $output
    ]) . "\n\n";
    
} catch (\Error $e) {
    echo json_encode([
        'error' => 'PHP Error',
        'type' => get_class($e),
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ]);
} catch (\Exception $e) {
    echo json_encode([
        'error' => 'Exception',
        'type' => get_class($e),
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ]);
}

echo "\n\n" . json_encode(['step' => 'Test completed']);
