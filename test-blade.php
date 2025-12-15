<?php
/**
 * Test Blade Rendering
 */

// Load WordPress
define('WP_USE_THEMES', false);
$wp_load_path = '/var/www/html/wp-load.php'; // Change this to your WordPress path

if (file_exists($wp_load_path)) {
    require_once($wp_load_path);
} else {
    echo "WordPress not found. Testing standalone.\n";
    define('ABSPATH', true);
}

// Load plugin
require_once __DIR__ . '/vendor/autoload.php';

define('ZARGAR_ACCOUNTING_VERSION', '1.0.0');
define('ZARGAR_ACCOUNTING_PLUGIN_DIR', __DIR__ . '/');
define('ZARGAR_ACCOUNTING_PLUGIN_URL', 'http://localhost/wp-content/plugins/zargar-accounting/');
define('ZARGAR_ACCOUNTING_PLUGIN_BASENAME', 'zargar-accounting/zargar-accounting.php');

try {
    // Test Logger
    echo "Testing Logger...\n";
    $logger = ZargarAccounting\Logger\Logger::getInstance();
    $logger->info('Test log entry');
    echo "✓ Logger works!\n\n";
    
    // Test BladeRenderer
    echo "Testing BladeRenderer...\n";
    $blade = ZargarAccounting\Core\BladeRenderer::getInstance();
    echo "✓ BladeRenderer initialized!\n\n";
    
    // Test simple template
    echo "Testing simple render...\n";
    $output = $blade->render('admin.dashboard', [
        'title' => 'Test Dashboard'
    ]);
    echo "✓ Template rendered successfully!\n";
    echo "Output length: " . strlen($output) . " characters\n\n";
    
    echo "All tests passed! ✓\n";
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
}
