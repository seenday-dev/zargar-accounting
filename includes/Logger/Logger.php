<?php
/**
 * Professional Logger System
 * 
 * @package ZargarAccounting
 * @since 1.0.0
 */

namespace ZargarAccounting\Logger;

if (!defined('ABSPATH')) {
    exit;
}

class Logger {
    private static $instance = null;
    private $log_dir;
    private $max_file_size = 5242880; // 5MB
    private $max_files = 10;
    
    // Log levels
    const EMERGENCY = 'EMERGENCY';
    const ALERT = 'ALERT';
    const CRITICAL = 'CRITICAL';
    const ERROR = 'ERROR';
    const WARNING = 'WARNING';
    const NOTICE = 'NOTICE';
    const INFO = 'INFO';
    const DEBUG = 'DEBUG';
    
    private function __construct() {
        $this->log_dir = ZARGAR_ACCOUNTING_PLUGIN_DIR . 'storage/logs';
        $this->ensureLogDirectory();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function ensureLogDirectory() {
        if (!file_exists($this->log_dir)) {
            wp_mkdir_p($this->log_dir);
        }
        
        // Create .htaccess to protect log files
        $htaccess_file = $this->log_dir . '/.htaccess';
        if (!file_exists($htaccess_file)) {
            file_put_contents($htaccess_file, "Deny from all\n");
        }
        
        // Create index.php to prevent directory listing
        $index_file = $this->log_dir . '/index.php';
        if (!file_exists($index_file)) {
            file_put_contents($index_file, "<?php\n// Silence is golden.\n");
        }
    }
    
    private function write($level, $message, $context = []) {
        $log_file = $this->getLogFile();
        
        // Check file size and rotate if needed
        if (file_exists($log_file) && filesize($log_file) > $this->max_file_size) {
            $this->rotateLogFile($log_file);
        }
        
        $timestamp = function_exists('current_time') ? current_time('Y-m-d H:i:s') : date('Y-m-d H:i:s');
        $user_id = function_exists('get_current_user_id') ? get_current_user_id() : 0;
        $user = $user_id && function_exists('get_userdata') ? get_userdata($user_id)->user_login : 'guest';
        
        // Format context
        $context_str = !empty($context) ? ' | Context: ' . json_encode($context, JSON_UNESCAPED_UNICODE) : '';
        
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
        error_log($log_entry, 3, $log_file);
        
        // If it's an error level, also log to WordPress debug log if enabled
        if (in_array($level, [self::ERROR, self::CRITICAL, self::ALERT, self::EMERGENCY]) && defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) {
            error_log("Zargar Accounting [{$level}]: {$message}");
        }
    }
    
    private function getLogFile() {
        $date = function_exists('current_time') ? current_time('Y-m-d') : date('Y-m-d');
        return $this->log_dir . "/zargar-{$date}.log";
    }
    
    private function rotateLogFile($log_file) {
        $pathinfo = pathinfo($log_file);
        $base_name = $pathinfo['filename'];
        $extension = $pathinfo['extension'];
        $dir = $pathinfo['dirname'];
        
        // Find existing rotated files
        $rotated_files = glob($dir . '/' . $base_name . '-*.{' . $extension . '}', GLOB_BRACE);
        
        // Sort by modification time
        usort($rotated_files, function($a, $b) {
            return filemtime($a) - filemtime($b);
        });
        
        // Delete old files if we have too many
        if (count($rotated_files) >= $this->max_files) {
            $files_to_delete = array_slice($rotated_files, 0, count($rotated_files) - $this->max_files + 1);
            foreach ($files_to_delete as $file) {
                unlink($file);
            }
        }
        
        // Rotate current file
        $rotated_name = $dir . '/' . $base_name . '-' . time() . '.' . $extension;
        rename($log_file, $rotated_name);
    }
    
    // Public logging methods
    public function emergency($message, $context = []) {
        $this->write(self::EMERGENCY, $message, $context);
    }
    
    public function alert($message, $context = []) {
        $this->write(self::ALERT, $message, $context);
    }
    
    public function critical($message, $context = []) {
        $this->write(self::CRITICAL, $message, $context);
    }
    
    public function error($message, $context = []) {
        $this->write(self::ERROR, $message, $context);
    }
    
    public function warning($message, $context = []) {
        $this->write(self::WARNING, $message, $context);
    }
    
    public function notice($message, $context = []) {
        $this->write(self::NOTICE, $message, $context);
    }
    
    public function info($message, $context = []) {
        $this->write(self::INFO, $message, $context);
    }
    
    public function debug($message, $context = []) {
        // Only log debug in development mode
        if (defined('WP_DEBUG') && WP_DEBUG) {
            $this->write(self::DEBUG, $message, $context);
        }
    }
    
    /**
     * Get recent log entries
     */
    public function getRecentLogs($limit = 100, $level = null) {
        $log_file = $this->getLogFile();
        
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
        
        return array_slice($lines, 0, $limit);
    }
    
    /**
     * Clear old logs
     */
    public function clearOldLogs($days = 30) {
        $files = glob($this->log_dir . '/*.log');
        $time = time();
        $deleted = 0;
        
        foreach ($files as $file) {
            if (is_file($file)) {
                if ($time - filemtime($file) >= (60 * 60 * 24 * $days)) {
                    unlink($file);
                    $deleted++;
                }
            }
        }
        
        $this->info("Cleared {$deleted} old log files");
        return $deleted;
    }
}
