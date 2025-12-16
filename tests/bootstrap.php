<?php
/**
 * Test Bootstrap File
 * 
 * @package ZargarAccounting
 * @subpackage Tests
 */

// Define test constants
define('ZARGAR_ACCOUNTING_TESTS', true);
define('ZARGAR_ACCOUNTING_PLUGIN_DIR', dirname(__DIR__) . '/');
define('ZARGAR_ACCOUNTING_PLUGIN_URL', 'http://localhost/');
define('ZARGAR_ACCOUNTING_VERSION', '1.0.0');

// Load Composer autoloader
require_once ZARGAR_ACCOUNTING_PLUGIN_DIR . 'vendor/autoload.php';

// Mock WordPress functions for testing
if (!function_exists('wp_mkdir_p')) {
    function wp_mkdir_p($target) {
        return @mkdir($target, 0755, true);
    }
}

if (!function_exists('current_time')) {
    function current_time($type, $gmt = 0) {
        return ($type === 'timestamp') ? time() : date('Y-m-d H:i:s');
    }
}

if (!function_exists('get_current_user_id')) {
    function get_current_user_id() {
        return 1;
    }
}

if (!function_exists('get_userdata')) {
    function get_userdata($user_id) {
        return (object) ['user_login' => 'test_user'];
    }
}

if (!function_exists('admin_url')) {
    function admin_url($path = '') {
        return 'http://localhost/wp-admin/' . $path;
    }
}

if (!function_exists('plugin_dir_path')) {
    function plugin_dir_path($file) {
        return dirname($file) . '/';
    }
}

if (!function_exists('plugin_dir_url')) {
    function plugin_dir_url($file) {
        return 'http://localhost/wp-content/plugins/' . basename(dirname($file)) . '/';
    }
}

if (!function_exists('wp_create_nonce')) {
    function wp_create_nonce($action) {
        return md5($action . 'test_nonce');
    }
}

if (!function_exists('check_ajax_referer')) {
    function check_ajax_referer($action, $query_arg = false, $die = true) {
        return true;
    }
}

if (!function_exists('wp_send_json_success')) {
    function wp_send_json_success($data = null) {
        echo json_encode(['success' => true, 'data' => $data]);
        exit;
    }
}

if (!function_exists('wp_send_json_error')) {
    function wp_send_json_error($data = null) {
        echo json_encode(['success' => false, 'data' => $data]);
        exit;
    }
}

if (!function_exists('sanitize_text_field')) {
    function sanitize_text_field($str) {
        return strip_tags($str);
    }
}

if (!function_exists('current_user_can')) {
    function current_user_can($capability) {
        return true;
    }
}

if (!defined('ABSPATH')) {
    define('ABSPATH', '/tmp/wordpress/');
}

if (!defined('WP_DEBUG_LOG')) {
    define('WP_DEBUG_LOG', false);
}

echo "✓ Test bootstrap loaded successfully\n";
