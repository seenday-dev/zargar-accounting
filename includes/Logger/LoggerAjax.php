<?php
/**
 * Logger AJAX Handlers
 * 
 * @package ZargarAccounting
 * @since 2.0.0
 */

namespace ZargarAccounting\Logger;

if (!defined('ABSPATH')) {
    exit;
}

class LoggerAjax {
    private static $instance = null;
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function registerHooks() {
        add_action('wp_ajax_zargar_get_logs', [$this, 'getLogs']);
        add_action('wp_ajax_zargar_clear_logs', [$this, 'clearLogs']);
    }
    
    /**
     * Get logs via AJAX
     */
    public function getLogs() {
        try {
            // Verify nonce
            if (!check_ajax_referer('zargar_logs_nonce', 'nonce', false)) {
                wp_send_json_error(['message' => 'عدم دسترسی - nonce نامعتبر']);
                return;
            }
            
            // Get parameters
            $type = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : 'product';
            $level = isset($_POST['level']) ? sanitize_text_field($_POST['level']) : null;
            $limit = isset($_POST['limit']) ? intval($_POST['limit']) : 100;
            
            // Validate type
            $valid_types = [
                MonologManager::CHANNEL_PRODUCT,
                MonologManager::CHANNEL_SALES,
                MonologManager::CHANNEL_PRICE,
                MonologManager::CHANNEL_ERROR
            ];
            
            if (!in_array($type, $valid_types)) {
                wp_send_json_error(['message' => 'نوع لاگ نامعتبر است: ' . $type]);
                return;
            }
            
            // Get logger instance
            $logger = MonologManager::getInstance();
            
            // Get logs
            $logs = $logger->getRecentLogs($type, $limit);
            
            // Get stats for all types
            $stats = [];
            foreach ($valid_types as $stat_type) {
                $stats[$stat_type] = $logger->getStats($stat_type);
            }
            
            // Send success response
            wp_send_json_success([
                'logs' => $logs,
                'stats' => $stats,
                'type' => $type,
                'count' => count($logs),
                'debug' => [
                    'channel' => $type,
                    'limit' => $limit,
                    'total_stats' => array_sum(array_column($stats, 'total'))
                ]
            ]);
        } catch (\Exception $e) {
            wp_send_json_error([
                'message' => 'خطا در دریافت لاگ‌ها: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    
    /**
     * Clear logs via AJAX
     */
    public function clearLogs() {
        // Verify nonce
        if (!check_ajax_referer('zargar_logs_nonce', 'nonce', false)) {
            wp_send_json_error(['message' => 'عدم دسترسی']);
            return;
        }
        
        // Check capability
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'شما دسترسی لازم را ندارید']);
            return;
        }
        
        // Get type
        $type = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : 'product';
        
        // Validate type
        $valid_types = [
            MonologManager::CHANNEL_PRODUCT,
            MonologManager::CHANNEL_SALES,
            MonologManager::CHANNEL_PRICE,
            MonologManager::CHANNEL_ERROR
        ];
        
        if (!in_array($type, $valid_types)) {
            wp_send_json_error(['message' => 'نوع لاگ نامعتبر است']);
            return;
        }
        
        // Get logger instance
        $logger = MonologManager::getInstance();
        
        // Clear logs
        $deleted = $logger->clearLogs($type);
        
        wp_send_json_success([
            'message' => sprintf('%d فایل لاگ حذف شد', $deleted),
            'deleted' => $deleted
        ]);
    }
}
