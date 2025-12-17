<?php
/**
 * Zargar API Client
 * 
 * Handles communication with Zargar Accounting Server
 * 
 * @package ZargarAccounting
 * @since 2.0.0
 */

namespace ZargarAccounting\API;

use ZargarAccounting\Logger\MonologManager;

if (!defined('ABSPATH')) {
    exit;
}

class ZargarApiClient {
    private static $instance = null;
    private $logger;
    private $config;
    
    private function __construct() {
        $this->logger = MonologManager::getInstance();
        $this->loadConfig();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Load configuration from WordPress options
     */
    private function loadConfig(): void {
        $this->config = [
            'host' => get_option('zargar_server_ip', ''),
            'port' => get_option('zargar_server_port', '8080'),
            'username' => get_option('zargar_username', ''),
            'password' => get_option('zargar_password', '')
        ];
    }
    
    /**
     * Test connection and get UserKey
     * 
     * @return array ['success' => bool, 'message' => string, 'userkey' => string|null, 'data' => array]
     */
    public function testConnection(): array {
        // Validate configuration
        if (empty($this->config['host']) || empty($this->config['port'])) {
            $this->logger->error('API Client not initialized - missing configuration');
            return [
                'success' => false,
                'message' => 'تنظیمات اتصال ناقص است. لطفاً IP و پورت را وارد کنید.',
                'userkey' => null,
                'data' => []
            ];
        }
        
        try {
            $this->logger->product('تلاش برای اتصال به سرور حسابداری', [
                'host' => $this->config['host'],
                'port' => $this->config['port'],
                'username' => $this->config['username']
            ]);
            
            // Build URL with query parameters
            $url = sprintf(
                'http://%s:%d/services/login/?username=%s&password=%s',
                $this->config['host'],
                $this->config['port'],
                urlencode($this->config['username']),
                urlencode($this->config['password'])
            );
            
            // Send request using WordPress HTTP API
            $response = wp_remote_get($url, [
                'timeout' => 20,
                'sslverify' => false,
                'headers' => [
                    'Accept' => 'application/json',
                    'User-Agent' => 'Zargar-Accounting-Plugin/2.0'
                ]
            ]);
            
            // Check for WP_Error
            if (is_wp_error($response)) {
                $error_message = $response->get_error_message();
                $this->logger->error('خطای شبکه در اتصال به سرور', [
                    'error' => $error_message
                ]);
                
                return [
                    'success' => false,
                    'message' => 'خطا در اتصال به سرور: ' . $error_message,
                    'userkey' => null,
                    'data' => ['error' => $error_message]
                ];
            }
            
            // Get response details
            $statusCode = wp_remote_retrieve_response_code($response);
            $body = wp_remote_retrieve_body($response);
            
            if ($statusCode !== 200) {
                $this->logger->error('خطا در اتصال به سرور', [
                    'status_code' => $statusCode,
                    'response' => substr($body, 0, 500)
                ]);
                
                return [
                    'success' => false,
                    'message' => sprintf('خطای سرور: کد %d', $statusCode),
                    'userkey' => null,
                    'data' => ['status_code' => $statusCode]
                ];
            }
            
            // Decode JSON response
            $decoded = json_decode($body, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->logger->error('خطا در پردازش پاسخ JSON', [
                    'json_error' => json_last_error_msg(),
                    'response' => substr($body, 0, 500)
                ]);
                
                return [
                    'success' => false,
                    'message' => 'پاسخ سرور قابل خواندن نیست',
                    'userkey' => null,
                    'data' => ['json_error' => json_last_error_msg()]
                ];
            }
            
            // Check if Status is OK
            if (!isset($decoded['Status']) || $decoded['Status'] !== 'OK') {
                $this->logger->warning('پاسخ سرور با خطا مواجه شد', [
                    'status' => $decoded['Status'] ?? 'نامشخص',
                    'response' => $decoded
                ]);
                
                return [
                    'success' => false,
                    'message' => 'نام کاربری یا رمز عبور اشتباه است',
                    'userkey' => null,
                    'data' => $decoded
                ];
            }
            
            // Try different possible locations for UserKey
            $userKey = $decoded['Result']['UserKey'] 
                    ?? $decoded['Message']['UserKey'] 
                    ?? $decoded['UserKey'] 
                    ?? null;
            
            if (!$userKey) {
                $this->logger->warning('UserKey یافت نشد در پاسخ', [
                    'response_keys' => array_keys($decoded)
                ]);
                
                return [
                    'success' => false,
                    'message' => 'UserKey در پاسخ سرور یافت نشد',
                    'userkey' => null,
                    'data' => $decoded
                ];
            }
            
            // Success - extract user info
            $firstName = $decoded['Result']['FirstName'] ?? '';
            $lastName = $decoded['Result']['LastName'] ?? '';
            $fullName = trim($firstName . ' ' . $lastName);
            
            $this->logger->product('اتصال به سرور موفقیت‌آمیز بود', [
                'userkey_preview' => substr($userKey, 0, 8) . '...',
                'server' => $this->config['host'] . ':' . $this->config['port'],
                'user' => $fullName
            ]);
            
            return [
                'success' => true,
                'message' => 'اتصال موفقیت‌آمیز به سرور حسابداری زرگر',
                'userkey' => $userKey,
                'data' => [
                    'server' => $this->config['host'] . ':' . $this->config['port'],
                    'username' => $this->config['username'],
                    'fullname' => $fullName,
                    'user_id' => $decoded['Result']['UserId'] ?? '',
                    'business_id' => $decoded['BusinessId'] ?? '',
                    'version' => $decoded['Version'] ?? '',
                    'response' => $decoded
                ]
            ];
            
        } catch (\Exception $e) {
            $this->logger->error('خطای غیرمنتظره در اتصال به سرور', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return [
                'success' => false,
                'message' => 'خطا در اتصال به سرور: ' . $e->getMessage(),
                'userkey' => null,
                'data' => [
                    'error_code' => $e->getCode(),
                    'error_message' => $e->getMessage()
                ]
            ];
        }
    }
    
    /**
     * Get UserKey for authenticated requests
     * 
     * @return string|null
     */
    public function getUserKey(): ?string {
        $result = $this->testConnection();
        return $result['userkey'] ?? null;
    }
    
    /**
     * Reload configuration (useful after settings update)
     */
    public function reloadConfig(): void {
        $this->loadConfig();
    }
}
