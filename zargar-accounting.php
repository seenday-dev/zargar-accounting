<?php
/**
 * Plugin Name: Zargar Accounting
 * Plugin URI: https://seenday.com
 * Description: WordPress integration for Zargar Accounting system
 * Version: 1.0.0
 * Author: Seenday
 * Author URI: https://seenday.com
 * Text Domain: zargar-accounting
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 8.1
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('ZARGAR_ACCOUNTING_VERSION', '1.0.0');
define('ZARGAR_ACCOUNTING_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ZARGAR_ACCOUNTING_PLUGIN_URL', plugin_dir_url(__FILE__));
define('ZARGAR_ACCOUNTING_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Autoload Composer dependencies
if (file_exists(ZARGAR_ACCOUNTING_PLUGIN_DIR . 'vendor/autoload.php')) {
    require_once ZARGAR_ACCOUNTING_PLUGIN_DIR . 'vendor/autoload.php';
}

// Initialize the plugin
function zargar_accounting_init() {
    // Initialize Monolog Manager
    $logger = ZargarAccounting\Logger\MonologManager::getInstance();
    
    // Initialize Blade Renderer
    $blade = ZargarAccounting\Core\BladeRenderer::getInstance();
    
    // Initialize Admin Managers
    $menu_manager = ZargarAccounting\Admin\MenuManager::getInstance();
    $menu_manager->registerHooks();
    
    $assets_manager = ZargarAccounting\Admin\AssetsManager::getInstance();
    $assets_manager->registerHooks();
    
    // Initialize AJAX handlers
    $logger_ajax = ZargarAccounting\Logger\LoggerAjax::getInstance();
    $logger_ajax->registerHooks();
    
    // Log plugin initialization
    $logger->info('Zargar Accounting plugin initialized');
}

add_action('plugins_loaded', 'zargar_accounting_init');

// Activation hook
register_activation_hook(__FILE__, 'zargar_accounting_activate');

function zargar_accounting_activate() {
    // Create necessary directories
    $upload_dir = wp_upload_dir();
    $zargar_dir = $upload_dir['basedir'] . '/zargar-accounting';
    
    if (!file_exists($zargar_dir)) {
        wp_mkdir_p($zargar_dir);
        wp_mkdir_p($zargar_dir . '/logs');
        wp_mkdir_p($zargar_dir . '/cache');
    }
    
    flush_rewrite_rules();
}

// Deactivation hook
register_deactivation_hook(__FILE__, 'zargar_accounting_deactivate');

function zargar_accounting_deactivate() {
    flush_rewrite_rules();
}
