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
    // Load logger
    $logger = ZargarAccounting\Logger\Logger::getInstance();
    
    // Load Blade renderer
    $blade = ZargarAccounting\Core\BladeRenderer::getInstance();
    
    // Add admin menu
    add_action('admin_menu', 'zargar_accounting_admin_menu');
    
    // Log plugin initialization
    $logger->info('Zargar Accounting plugin initialized');
}

add_action('plugins_loaded', 'zargar_accounting_init');

// Add admin menu
function zargar_accounting_admin_menu() {
    add_menu_page(
        __('حسابداری زرگر', 'zargar-accounting'),
        __('حسابداری زرگر', 'zargar-accounting'),
        'manage_options',
        'zargar-accounting',
        'zargar_accounting_render_dashboard',
        'dashicons-calculator',
        30
    );
    
    add_submenu_page(
        'zargar-accounting',
        __('داشبورد', 'zargar-accounting'),
        __('داشبورد', 'zargar-accounting'),
        'manage_options',
        'zargar-accounting',
        'zargar_accounting_render_dashboard'
    );
    
    add_submenu_page(
        'zargar-accounting',
        __('همگام‌سازی', 'zargar-accounting'),
        __('همگام‌سازی', 'zargar-accounting'),
        'manage_options',
        'zargar-accounting-sync',
        'zargar_accounting_render_sync'
    );
    
    add_submenu_page(
        'zargar-accounting',
        __('گزارش‌ها', 'zargar-accounting'),
        __('گزارش‌ها', 'zargar-accounting'),
        'manage_options',
        'zargar-accounting-logs',
        'zargar_accounting_render_logs'
    );
}

// Render dashboard page
function zargar_accounting_render_dashboard() {
    $blade = ZargarAccounting\Core\BladeRenderer::getInstance();
    echo $blade->render('admin.dashboard', [
        'title' => 'داشبورد'
    ]);
}

// Render sync page
function zargar_accounting_render_sync() {
    $blade = ZargarAccounting\Core\BladeRenderer::getInstance();
    echo $blade->render('admin.sync-status', [
        'title' => 'همگام‌سازی',
        'sync_progress' => 0,
        'sync_message' => 'آماده برای شروع',
        'last_sync' => 'هرگز',
        'success_count' => 0,
        'failed_count' => 0,
        'last_error' => 'ندارد'
    ]);
}

// Render logs page
function zargar_accounting_render_logs() {
    $logger = ZargarAccounting\Logger\Logger::getInstance();
    $log_entries = $logger->getRecentLogs(50);
    
    // Parse log entries
    $logs = [];
    foreach ($log_entries as $entry) {
        if (preg_match('/\[(.*?)\] \[(.*?)\] \[User: (.*?)\] (.*)/', $entry, $matches)) {
            $logs[] = [
                'time' => $matches[1],
                'level' => $matches[2],
                'user' => $matches[3],
                'message' => $matches[4]
            ];
        }
    }
    
    $blade = ZargarAccounting\Core\BladeRenderer::getInstance();
    echo $blade->render('admin.logs', [
        'title' => 'گزارش‌ها',
        'logs' => $logs
    ]);
}

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
