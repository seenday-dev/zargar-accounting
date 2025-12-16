<?php
/**
 * Advanced Logger System with Multiple Log Types
 * 
 * @package ZargarAccounting
 * @since 1.0.0
 */

namespace ZargarAccounting\Logger;

if (!defined('ABSPATH')) {
    exit;
}

class AdvancedLogger {
    private static $instance = null;
    private $log_dir;
    private $max_file_size = 5242880; // 5MB
    private $max_files = 30;
    
    // Log types
    const TYPE_PRODUCT = 'product';
    const TYPE_SALES = 'sales';
    const TYPE_PRICE = 'price';
    const TYPE_ERROR = 'error';
    
    // Log levels
    const LEVEL_INFO = 'INFO';
    const LEVEL_SUCCESS = 'SUCCESS';
    const LEVEL_WARNING = 'WARNING';
    const LEVEL_ERROR = 'ERROR';
    
    private function __construct() {
        // Determine plugin directory
        if (defined('ZARGAR_ACCOUNTING_PLUGIN_DIR')) {
            $plugin_dir = ZARGAR_ACCOUNTING_PLUGIN_DIR;
        } else {
            // Fallback: calculate from current file location
            $plugin_dir = dirname(dirname(__DIR__)) . '/';
        }
        
        $this->log_dir = $plugin_dir . 'storage/logs';
        $this->ensureLogDirectories();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function ensureLogDirectories() {
        $types = [self::TYPE_PRODUCT, self::TYPE_SALES, self::TYPE_PRICE, self::TYPE_ERROR];
        
        foreach ($types as $type) {
            $type_dir = $this->log_dir . '/' . $type;
            if (!file_exists($type_dir)) {
                wp_mkdir_p($type_dir);
            }
        }
        
        // Protect log files
        $htaccess = $this->log_dir . '/.htaccess';
        if (!file_exists($htaccess)) {
            file_put_contents($htaccess, "Deny from all\n");
        }
    }
    
    /**
     * Log product synchronization
     */
    public function logProduct($message, $level = self::LEVEL_INFO, $context = []) {
        return $this->write(self::TYPE_PRODUCT, $level, $message, $context);
    }
    
    /**
     * Log sales transactions
     */
    public function logSales($message, $level = self::LEVEL_INFO, $context = []) {
        return $this->write(self::TYPE_SALES, $level, $message, $context);
    }
    
    /**
     * Log price updates
     */
    public function logPrice($message, $level = self::LEVEL_INFO, $context = []) {
        return $this->write(self::TYPE_PRICE, $level, $message, $context);
    }
    
    /**
     * Log errors
     */
    public function logError($message, $context = []) {
        return $this->write(self::TYPE_ERROR, self::LEVEL_ERROR, $message, $context);
    }
    
    /**
     * Write log entry
     */
    private function write($type, $level, $message, $context = []) {
        $log_file = $this->getLogFile($type);
        
        // Check and rotate if needed
        if (file_exists($log_file) && filesize($log_file) > $this->max_file_size) {
            $this->rotateLogFile($log_file);
        }
        
        $timestamp = function_exists('current_time') ? current_time('Y-m-d H:i:s') : date('Y-m-d H:i:s');
        $user_id = function_exists('get_current_user_id') ? get_current_user_id() : 0;
        $user = $user_id && function_exists('get_userdata') ? get_userdata($user_id)->user_login : 'system';
        
        // Format context
        $context_str = !empty($context) ? ' | ' . json_encode($context, JSON_UNESCAPED_UNICODE) : '';
        
        // Build log entry
        $log_entry = sprintf(
            "[%s] [%s] [User: %s] %s%s\n",
            $timestamp,
            $level,
            $user,
            $message,
            $context_str
        );
        
        // Write to file
        return error_log($log_entry, 3, $log_file);
    }
    
    private function getLogFile($type) {
        $date = function_exists('current_time') ? current_time('Y-m-d') : date('Y-m-d');
        return $this->log_dir . '/' . $type . '/zargar-' . $type . '-' . $date . '.log';
    }
    
    private function rotateLogFile($log_file) {
        $pathinfo = pathinfo($log_file);
        $dir = $pathinfo['dirname'];
        $base_name = $pathinfo['filename'];
        $extension = $pathinfo['extension'];
        
        $rotated_name = $dir . '/' . $base_name . '-' . time() . '.' . $extension;
        rename($log_file, $rotated_name);
        
        // Clean old files
        $this->cleanOldLogs($dir);
    }
    
    private function cleanOldLogs($dir) {
        $files = glob($dir . '/*.log');
        if (count($files) > $this->max_files) {
            usort($files, function($a, $b) {
                return filemtime($a) - filemtime($b);
            });
            
            $to_delete = array_slice($files, 0, count($files) - $this->max_files);
            foreach ($to_delete as $file) {
                unlink($file);
            }
        }
    }
    
    /**
     * Get logs by type
     */
    public function getLogs($type, $limit = 100, $level = null) {
        $log_file = $this->getLogFile($type);
        
        if (!file_exists($log_file)) {
            return [];
        }
        
        $lines = file($log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $lines = array_reverse($lines);
        
        if ($level) {
            $lines = array_filter($lines, function($line) use ($level) {
                return strpos($line, "[{$level}]") !== false;
            });
        }
        
        $logs = [];
        foreach (array_slice($lines, 0, $limit) as $line) {
            $parsed = $this->parseLogLine($line);
            if ($parsed) {
                $logs[] = $parsed;
            }
        }
        
        return $logs;
    }
    
    /**
     * Parse log line
     */
    private function parseLogLine($line) {
        // Pattern: [timestamp] [LEVEL] [User: username] message | context
        if (preg_match('/\[(.*?)\] \[(.*?)\] \[User: (.*?)\] (.*)/', $line, $matches)) {
            $message_and_context = $matches[4];
            $parts = explode(' | ', $message_and_context, 2);
            
            return [
                'timestamp' => $matches[1],
                'level' => $matches[2],
                'user' => $matches[3],
                'message' => $parts[0],
                'context' => isset($parts[1]) ? $parts[1] : null
            ];
        }
        
        return null;
    }
    
    /**
     * Get log statistics
     */
    public function getLogStats($type) {
        $log_file = $this->getLogFile($type);
        
        if (!file_exists($log_file)) {
            return [
                'total' => 0,
                'info' => 0,
                'success' => 0,
                'warning' => 0,
                'error' => 0
            ];
        }
        
        $lines = file($log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        $stats = [
            'total' => count($lines),
            'info' => 0,
            'success' => 0,
            'warning' => 0,
            'error' => 0
        ];
        
        foreach ($lines as $line) {
            if (strpos($line, '[INFO]') !== false) $stats['info']++;
            if (strpos($line, '[SUCCESS]') !== false) $stats['success']++;
            if (strpos($line, '[WARNING]') !== false) $stats['warning']++;
            if (strpos($line, '[ERROR]') !== false) $stats['error']++;
        }
        
        return $stats;
    }
    
    /**
     * Clear logs for a specific type
     */
    public function clearLogs($type) {
        $type_dir = $this->log_dir . '/' . $type;
        $files = glob($type_dir . '/*.log');
        
        $deleted = 0;
        foreach ($files as $file) {
            if (unlink($file)) {
                $deleted++;
            }
        }
        
        return $deleted;
    }
}
