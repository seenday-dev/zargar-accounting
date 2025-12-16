<?php
/**
 * Monolog Manager - Centralized Logging with Monolog
 * 
 * @package ZargarAccounting
 * @since 2.0.0
 */

namespace ZargarAccounting\Logger;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Formatter\LineFormatter;

if (!defined('ABSPATH')) {
    exit;
}

class MonologManager {
    private static $instance = null;
    private $loggers = [];
    private $log_dir;
    
    // Log channels
    const CHANNEL_PRODUCT = 'product';
    const CHANNEL_SALES = 'sales';
    const CHANNEL_PRICE = 'price';
    const CHANNEL_ERROR = 'error';
    const CHANNEL_GENERAL = 'general';
    
    private function __construct() {
        $this->log_dir = ZARGAR_ACCOUNTING_PLUGIN_DIR . 'storage/logs';
        $this->ensureLogDirectory();
        $this->initializeLoggers();
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
        
        // Create index.php
        $index_file = $this->log_dir . '/index.php';
        if (!file_exists($index_file)) {
            file_put_contents($index_file, "<?php\n// Silence is golden.\n");
        }
    }
    
    private function initializeLoggers() {
        $channels = [
            self::CHANNEL_PRODUCT,
            self::CHANNEL_SALES,
            self::CHANNEL_PRICE,
            self::CHANNEL_ERROR,
            self::CHANNEL_GENERAL
        ];
        
        foreach ($channels as $channel) {
            $this->loggers[$channel] = $this->createLogger($channel);
        }
    }
    
    private function createLogger($channel) {
        $logger = new Logger($channel);
        
        // Create channel directory
        $channel_dir = $this->log_dir . '/' . $channel;
        if (!file_exists($channel_dir)) {
            wp_mkdir_p($channel_dir);
        }
        
        // Rotating file handler (30 days, max 30 files)
        $handler = new RotatingFileHandler(
            $channel_dir . '/' . $channel . '.log',
            30,
            Logger::DEBUG
        );
        
        // Custom formatter
        $formatter = new LineFormatter(
            "[%datetime%] [%level_name%] [User: %extra.user%] %message% %context%\n",
            "Y-m-d H:i:s",
            true,
            true
        );
        $handler->setFormatter($formatter);
        
        // Add processor for user info
        $logger->pushProcessor(function ($record) {
            $user_id = function_exists('get_current_user_id') ? get_current_user_id() : 0;
            $record['extra']['user'] = $user_id && function_exists('get_userdata') 
                ? get_userdata($user_id)->user_login 
                : 'guest';
            return $record;
        });
        
        $logger->pushHandler($handler);
        
        return $logger;
    }
    
    /**
     * Get logger by channel
     */
    public function getLogger($channel = self::CHANNEL_GENERAL) {
        if (!isset($this->loggers[$channel])) {
            throw new \InvalidArgumentException("Invalid log channel: {$channel}");
        }
        return $this->loggers[$channel];
    }
    
    /**
     * Product logging methods
     */
    public function product($message, array $context = []) {
        $this->loggers[self::CHANNEL_PRODUCT]->info($message, $context);
    }
    
    public function productError($message, array $context = []) {
        $this->loggers[self::CHANNEL_PRODUCT]->error($message, $context);
    }
    
    public function productWarning($message, array $context = []) {
        $this->loggers[self::CHANNEL_PRODUCT]->warning($message, $context);
    }
    
    /**
     * Sales logging methods
     */
    public function sales($message, array $context = []) {
        $this->loggers[self::CHANNEL_SALES]->info($message, $context);
    }
    
    public function salesError($message, array $context = []) {
        $this->loggers[self::CHANNEL_SALES]->error($message, $context);
    }
    
    /**
     * Price logging methods
     */
    public function price($message, array $context = []) {
        $this->loggers[self::CHANNEL_PRICE]->info($message, $context);
    }
    
    public function priceError($message, array $context = []) {
        $this->loggers[self::CHANNEL_PRICE]->error($message, $context);
    }
    
    /**
     * Error logging methods
     */
    public function error($message, array $context = []) {
        $this->loggers[self::CHANNEL_ERROR]->error($message, $context);
    }
    
    public function critical($message, array $context = []) {
        $this->loggers[self::CHANNEL_ERROR]->critical($message, $context);
    }
    
    /**
     * General logging methods
     */
    public function info($message, array $context = []) {
        $this->loggers[self::CHANNEL_GENERAL]->info($message, $context);
    }
    
    public function debug($message, array $context = []) {
        $this->loggers[self::CHANNEL_GENERAL]->debug($message, $context);
    }
    
    public function warning($message, array $context = []) {
        $this->loggers[self::CHANNEL_GENERAL]->warning($message, $context);
    }
    
    /**
     * Get recent logs from a channel
     */
    public function getRecentLogs($channel = self::CHANNEL_GENERAL, $limit = 100) {
        $channel_dir = $this->log_dir . '/' . $channel;
        
        // Find all log files for this channel (including rotated ones)
        $log_files = glob($channel_dir . '/' . $channel . '*.log');
        
        if (empty($log_files)) {
            return [];
        }
        
        // Sort by modification time, newest first
        usort($log_files, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });
        
        $lines = [];
        
        // Read from newest files first until we have enough logs
        foreach ($log_files as $log_file) {
            if (count($lines) >= $limit) {
                break;
            }
            
            $file = new \SplFileObject($log_file, 'r');
            $file_lines = [];
            
            while (!$file->eof()) {
                $line = trim($file->fgets());
                if (!empty($line)) {
                    $parsed = $this->parseLogLine($line);
                    if ($parsed) {
                        $file_lines[] = $parsed;
                    }
                }
            }
            
            // Add file lines in reverse order (newest first)
            $lines = array_merge($lines, array_reverse($file_lines));
        }
        
        // Return only the requested limit
        return array_slice($lines, 0, $limit);
    }
    
    private function parseLogLine($line) {
        // Parse: [2025-12-15 10:30:45] [INFO] [User: admin] Message {context}
        if (preg_match('/\[(.*?)\] \[(.*?)\] \[User: (.*?)\] (.*?)( \{.*\})?$/', $line, $matches)) {
            return [
                'time' => $matches[1],
                'level' => $matches[2],
                'user' => $matches[3],
                'message' => $matches[4],
                'context' => isset($matches[5]) ? $matches[5] : ''
            ];
        }
        return null;
    }
    
    /**
     * Get log statistics
     */
    public function getStats($channel) {
        $channel_dir = $this->log_dir . '/' . $channel;
        
        // Find all log files for this channel
        $log_files = glob($channel_dir . '/' . $channel . '*.log');
        
        if (empty($log_files)) {
            return ['total' => 0, 'size' => 0, 'last_modified' => null];
        }
        
        $total_lines = 0;
        $total_size = 0;
        $latest_mtime = 0;
        
        foreach ($log_files as $log_file) {
            if (file_exists($log_file)) {
                $total_lines += count(file($log_file));
                $total_size += filesize($log_file);
                $mtime = filemtime($log_file);
                if ($mtime > $latest_mtime) {
                    $latest_mtime = $mtime;
                }
            }
        }
        
        return [
            'total' => $total_lines,
            'size' => $total_size,
            'last_modified' => $latest_mtime > 0 ? date('Y-m-d H:i:s', $latest_mtime) : null
        ];
    }
    
    /**
     * Clear logs for a channel
     */
    public function clearLogs($channel) {
        $channel_dir = $this->log_dir . '/' . $channel;
        
        if (!is_dir($channel_dir)) {
            return 0;
        }
        
        $files = glob($channel_dir . '/*.log*');
        $deleted = 0;
        
        foreach ($files as $file) {
            if (is_file($file) && unlink($file)) {
                $deleted++;
            }
        }
        
        return $deleted;
    }
}
