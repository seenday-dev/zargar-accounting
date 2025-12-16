<?php
/**
 * Simple Logs Viewer - No AJAX, Pure PHP
 * 
 * @package ZargarAccounting
 * @since 2.1.0
 */

namespace ZargarAccounting\Admin;

use ZargarAccounting\Logger\MonologManager;

if (!defined('ABSPATH')) {
    exit;
}

class LogsViewer {
    private static $instance = null;
    private $logger;
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->logger = MonologManager::getInstance();
    }
    
    /**
     * Render logs page - Simple PHP, No AJAX
     */
    public function render() {
        // Get current channel from URL
        $current_channel = isset($_GET['channel']) ? sanitize_text_field($_GET['channel']) : 'product';
        
        // Validate channel
        $valid_channels = [
            'product' => '📦 محصولات',
            'sales' => '💰 فروش',
            'price' => '💵 قیمت',
            'error' => '⚠️ خطاها'
        ];
        
        if (!isset($valid_channels[$current_channel])) {
            $current_channel = 'product';
        }
        
        // Get logs
        $logs = $this->logger->getRecentLogs($current_channel, 100);
        
        // Get stats for all channels
        $stats = [];
        foreach (array_keys($valid_channels) as $channel) {
            $stats[$channel] = $this->logger->getStats($channel);
        }
        
        // Render
        include ZARGAR_ACCOUNTING_PLUGIN_DIR . 'templates/admin/simple-logs.php';
    }
    
    /**
     * Format log level with color
     */
    public function formatLevel($level) {
        $colors = [
            'INFO' => '#3b82f6',
            'DEBUG' => '#6b7280',
            'WARNING' => '#f59e0b',
            'ERROR' => '#ef4444',
            'CRITICAL' => '#dc2626'
        ];
        
        $color = isset($colors[$level]) ? $colors[$level] : '#6b7280';
        
        return sprintf(
            '<span style="background: %s; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold;">%s</span>',
            $color,
            $level
        );
    }
    
    /**
     * Format context JSON
     */
    public function formatContext($context) {
        if (empty($context)) {
            return '-';
        }
        
        $context = trim($context);
        if (substr($context, 0, 1) === '{') {
            $decoded = json_decode($context, true);
            if ($decoded) {
                $html = '<details style="cursor: pointer;"><summary style="color: #3b82f6;">مشاهده جزئیات</summary><pre style="background: #f3f4f6; padding: 10px; border-radius: 4px; margin-top: 5px; font-size: 11px; overflow-x: auto;">';
                $html .= htmlspecialchars(json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                $html .= '</pre></details>';
                return $html;
            }
        }
        
        return '<code>' . htmlspecialchars($context) . '</code>';
    }
}
