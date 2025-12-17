<?php
/**
 * Settings Handler
 * 
 * Handles saving settings and AJAX connection test
 * 
 * @package ZargarAccounting
 * @since 2.0.0
 */

namespace ZargarAccounting\Admin;

use ZargarAccounting\API\ZargarApiClient;
use ZargarAccounting\Logger\MonologManager;

if (!defined('ABSPATH')) {
    exit;
}

class SettingsHandler {
    private static $instance = null;
    private $logger;
    
    private function __construct() {
        $this->logger = MonologManager::getInstance();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function registerHooks(): void {
        add_action('admin_post_zargar_save_settings', [$this, 'saveSettings']);
        add_action('wp_ajax_zargar_test_connection', [$this, 'ajaxTestConnection']);
    }
    
    /**
     * Save settings from POST request
     */
    public function saveSettings(): void {
        // Verify nonce
        if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'zargar_settings')) {
            wp_die('عدم دسترسی - نشانه امنیتی نامعتبر است');
        }
        
        // Check capability
        if (!current_user_can('manage_options')) {
            wp_die('شما دسترسی لازم را ندارید');
        }
        
        // Sanitize and save
        $server_ip = isset($_POST['server_ip']) ? sanitize_text_field($_POST['server_ip']) : '';
        $server_port = isset($_POST['server_port']) ? intval($_POST['server_port']) : 8080;
        $username = isset($_POST['username']) ? sanitize_text_field($_POST['username']) : '';
        $password = isset($_POST['password']) ? sanitize_text_field($_POST['password']) : '';
        
        update_option('zargar_server_ip', $server_ip);
        update_option('zargar_server_port', $server_port);
        update_option('zargar_username', $username);
        update_option('zargar_password', $password);
        
        $this->logger->product('تنظیمات اتصال به‌روزرسانی شد', [
            'server_ip' => $server_ip,
            'server_port' => $server_port,
            'username' => $username
        ]);
        
        // Redirect back with success message
        wp_redirect(add_query_arg([
            'page' => 'zargar-accounting-settings',
            'updated' => 'true'
        ], admin_url('admin.php')));
        exit;
    }
    
    /**
     * AJAX handler for testing connection
     */
    public function ajaxTestConnection(): void {
        // Set proper headers
        header('Content-Type: application/json; charset=utf-8');
        
        // Log for debugging
        error_log('Zargar: ajaxTestConnection called');
        error_log('Zargar: POST data: ' . print_r($_POST, true));
        
        // Verify nonce
        if (!check_ajax_referer('zargar_test_connection', 'nonce', false)) {
            error_log('Zargar: Nonce verification failed');
            wp_send_json_error([
                'message' => 'عدم دسترسی - نشانه امنیتی نامعتبر است'
            ]);
            return;
        }
        
        error_log('Zargar: Nonce verified successfully');
        
        // Check capability
        if (!current_user_can('manage_options')) {
            wp_send_json_error([
                'message' => 'شما دسترسی لازم را ندارید'
            ]);
            return;
        }
        
        // Get settings from AJAX request (to test before saving)
        $server_ip = isset($_POST['server_ip']) ? sanitize_text_field($_POST['server_ip']) : '';
        $server_port = isset($_POST['server_port']) ? intval($_POST['server_port']) : 8080;
        $username = isset($_POST['username']) ? sanitize_text_field($_POST['username']) : '';
        $password = isset($_POST['password']) ? sanitize_text_field($_POST['password']) : '';
        
        // Validate inputs
        if (empty($server_ip) || empty($server_port) || empty($username) || empty($password)) {
            error_log('Zargar: Missing required fields');
            wp_send_json_error([
                'message' => 'لطفاً همه فیلدها را پر کنید'
            ]);
            return;
        }
        
        // Temporarily save settings for test
        update_option('zargar_server_ip', $server_ip);
        update_option('zargar_server_port', $server_port);
        update_option('zargar_username', $username);
        update_option('zargar_password', $password);
        
        error_log('Zargar: Settings saved, testing connection...');
        
        try {
            // Test connection using full namespace
            $apiClient = \ZargarAccounting\API\ZargarApiClient::getInstance();
            error_log('Zargar: API Client instance created');
            
            $apiClient->reloadConfig();
            error_log('Zargar: Config reloaded');
            
            $result = $apiClient->testConnection();
            error_log('Zargar: Connection test result: ' . json_encode($result));
            
            if ($result['success']) {
                $response = [
                    'message' => $result['message'],
                    'userkey' => $result['userkey'],
                    'server' => $result['data']['server'] ?? '',
                    'username' => $result['data']['username'] ?? '',
                    'fullname' => $result['data']['fullname'] ?? '',
                    'version' => $result['data']['version'] ?? ''
                ];
                error_log('Zargar: Sending success response');
                wp_send_json_success($response);
            } else {
                error_log('Zargar: Sending error response');
                wp_send_json_error([
                    'message' => $result['message'],
                    'details' => $result['data']
                ]);
            }
        } catch (\Throwable $e) {
            // Catch all errors including fatal ones
            error_log('Zargar: CRITICAL Exception: ' . $e->getMessage());
            error_log('Zargar: File: ' . $e->getFile() . ':' . $e->getLine());
            error_log('Zargar: Stack trace: ' . $e->getTraceAsString());
            
            wp_send_json_error([
                'message' => 'خطای داخلی: ' . $e->getMessage(),
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
        }
    }
}
