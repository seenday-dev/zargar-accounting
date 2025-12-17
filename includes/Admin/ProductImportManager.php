<?php
/**
 * Product Import Manager
 * 
 * مدیریت کامل ایمپورت محصولات از API زرگر به WooCommerce
 * 
 * @package ZargarAccounting
 * @since 2.0.0
 */

namespace ZargarAccounting\Admin;

use ZargarAccounting\API\ZargarApiClient;
use ZargarAccounting\Logger\MonologManager;
use ZargarAccounting\Helpers\FieldMapper;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;

if (!defined('ABSPATH')) {
    exit;
}

class ProductImportManager {
    private static $instance = null;
    private $logger;
    private $apiClient;
    
    private function __construct() {
        $this->logger = MonologManager::getInstance();
        $this->apiClient = ZargarApiClient::getInstance();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function registerHooks(): void {
        add_action('wp_ajax_zargar_get_import_stats', [$this, 'ajaxGetImportStats']);
        add_action('wp_ajax_zargar_start_import', [$this, 'ajaxStartImport']);
        add_action('wp_ajax_zargar_get_import_progress', [$this, 'ajaxGetImportProgress']);
        add_action('wp_ajax_zargar_clear_import_logs', [$this, 'ajaxClearImportLogs']);
        add_action('wp_ajax_zargar_search_products', [$this, 'ajaxSearchProducts']);
        add_action('wp_ajax_zargar_import_specific_products', [$this, 'ajaxImportSpecificProducts']);
    }
    
    /**
     * دریافت آمار محصولات از API
     */
    public function ajaxGetImportStats(): void {
        check_ajax_referer('zargar_import_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'دسترسی غیرمجاز']);
            return;
        }
        
        try {
            $stats = $this->getProductStats();
            wp_send_json_success($stats);
        } catch (\Exception $e) {
            $this->logger->error('خطا در دریافت آمار محصولات', [
                'error' => $e->getMessage()
            ]);
            wp_send_json_error(['message' => $e->getMessage()]);
        }
    }
    
    /**
     * شروع فرآیند ایمپورت
     */
    public function ajaxStartImport(): void {
        check_ajax_referer('zargar_import_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'دسترسی غیرمجاز']);
            return;
        }
        
        $selectedFields = isset($_POST['fields']) ? json_decode(stripslashes($_POST['fields']), true) : [];
        
        if (empty($selectedFields)) {
            wp_send_json_error(['message' => 'لطفاً حداقل یک فیلد انتخاب کنید']);
            return;
        }
        
        try {
            // ذخیره تنظیمات فیلدهای انتخاب شده
            update_option('zargar_import_selected_fields', $selectedFields);
            
            // شروع import در background
            $this->startBackgroundImport($selectedFields);
            
            wp_send_json_success([
                'message' => 'ایمپورت با موفقیت شروع شد',
                'fields' => $selectedFields
            ]);
        } catch (\Exception $e) {
            $this->logger->error('خطا در شروع ایمپورت', [
                'error' => $e->getMessage()
            ]);
            wp_send_json_error(['message' => $e->getMessage()]);
        }
    }
    
    /**
     * دریافت پیشرفت ایمپورت
     */
    public function ajaxGetImportProgress(): void {
        check_ajax_referer('zargar_import_nonce', 'nonce');
        
        $progress = get_option('zargar_import_progress', [
            'status' => 'idle',
            'total' => 0,
            'imported' => 0,
            'failed' => 0,
            'current_page' => 0,
            'message' => ''
        ]);
        
        wp_send_json_success($progress);
    }
    
    /**
     * پاک کردن لاگ‌های ایمپورت
     */
    public function ajaxClearImportLogs(): void {
        check_ajax_referer('zargar_import_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'دسترسی غیرمجاز']);
            return;
        }
        
        try {
            // پاک کردن فایل‌های لاگ محصولات
            $logDir = ZARGAR_ACCOUNTING_PLUGIN_DIR . 'storage/logs/product/';
            
            if (is_dir($logDir)) {
                $files = glob($logDir . 'product-*.log');
                foreach ($files as $file) {
                    if (is_file($file)) {
                        unlink($file);
                    }
                }
            }
            
            $this->logger->product('لاگ‌های ایمپورت پاک شد');
            
            wp_send_json_success(['message' => 'لاگ‌ها با موفقیت پاک شدند']);
        } catch (\Exception $e) {
            wp_send_json_error(['message' => 'خطا در پاک کردن لاگ‌ها']);
        }
    }
    
    /**
     * دریافت آمار محصولات از API
     */
    private function getProductStats(): array {
        $config = [
            'host' => get_option('zargar_server_ip'),
            'port' => get_option('zargar_server_port', 8090),
            'username' => get_option('zargar_username'),
            'password' => get_option('zargar_password'),
        ];
        
        if (empty($config['host']) || empty($config['username'])) {
            throw new \Exception('اطلاعات اتصال به سرور تنظیم نشده است');
        }
        
        $client = new Client([
            'base_uri' => sprintf('http://%s:%d', $config['host'], $config['port']),
            'timeout' => 20,
            'http_errors' => false,
            'headers' => ['Accept' => 'application/json'],
        ]);
        
        // دریافت UserKey
        $response = $client->get('/services/login/', [
            'query' => [
                'username' => $config['username'],
                'password' => $config['password'],
            ],
        ]);
        
        $body = (string) $response->getBody();
        $login = json_decode($body, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('خطا در دریافت اطلاعات ورود');
        }
        
        $token = $login['Result']['UserKey'] ?? $login['Message']['UserKey'] ?? null;
        
        if (!$token) {
            throw new \Exception('UserKey دریافت نشد');
        }
        
        // دریافت صفحه اول برای محاسبه تعداد کل
        $response = $client->get('/Services/MadeGold/List/', [
            'query' => [
                'userkey' => $token,
                'PageIndex' => 1,
                'PageCount' => 100,
            ],
        ]);
        
        $decoded = json_decode((string) $response->getBody(), true);
        
        if (!isset($decoded['Result']) || !is_array($decoded['Result'])) {
            throw new \Exception('پاسخ API نامعتبر است');
        }
        
        $firstPageCount = count($decoded['Result']);
        
        // تخمین تعداد کل (با فرض اینکه صفحات پر هستند)
        // در واقع باید تا آخر صفحات را بگیریم ولی برای سرعت تخمین می‌زنیم
        $estimatedTotal = $firstPageCount > 0 ? 'تخمین: ' . ($firstPageCount * 10) . '+' : '0';
        
        return [
            'total' => $estimatedTotal,
            'first_page_count' => $firstPageCount,
            'available_fields' => FieldMapper::getAvailableFields(),
            'userkey' => substr($token, 0, 20) . '...'
        ];
    }
    
    /**
     * شروع ایمپورت در background
     */
    private function startBackgroundImport(array $selectedFields): void {
        // Reset progress
        update_option('zargar_import_progress', [
            'status' => 'running',
            'total' => 0,
            'imported' => 0,
            'failed' => 0,
            'current_page' => 1,
            'message' => 'در حال دریافت محصولات...'
        ]);
        
        // Schedule background task
        wp_schedule_single_event(time(), 'zargar_process_import', [$selectedFields]);
    }
    
    /**
     * پردازش ایمپورت محصولات (برای background job)
     */
    public function processImport(array $selectedFields): void {
        $this->logger->product('شروع ایمپورت محصولات', [
            'selected_fields' => $selectedFields
        ]);
        
        try {
            $config = [
                'host' => get_option('zargar_server_ip'),
                'port' => get_option('zargar_server_port', 8090),
                'username' => get_option('zargar_username'),
                'password' => get_option('zargar_password'),
            ];
            
            $products = $this->fetchAllProducts($config);
            
            $imported = 0;
            $failed = 0;
            $total = count($products);
            
            foreach ($products as $index => $product) {
                try {
                    $this->importSingleProduct($product, $selectedFields);
                    $imported++;
                    
                    // به‌روزرسانی progress
                    update_option('zargar_import_progress', [
                        'status' => 'running',
                        'total' => $total,
                        'imported' => $imported,
                        'failed' => $failed,
                        'current_page' => 0,
                        'message' => sprintf('در حال ایمپورت محصول %d از %d', $imported, $total)
                    ]);
                    
                } catch (\Exception $e) {
                    $failed++;
                    $this->logger->error('خطا در ایمپورت محصول', [
                        'product_id' => $product['ProductId'] ?? 'unknown',
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            // اتمام ایمپورت
            update_option('zargar_import_progress', [
                'status' => 'completed',
                'total' => $total,
                'imported' => $imported,
                'failed' => $failed,
                'current_page' => 0,
                'message' => 'ایمپورت با موفقیت تکمیل شد'
            ]);
            
            $this->logger->product('ایمپورت محصولات تکمیل شد', [
                'total' => $total,
                'imported' => $imported,
                'failed' => $failed
            ]);
            
        } catch (\Exception $e) {
            update_option('zargar_import_progress', [
                'status' => 'error',
                'total' => 0,
                'imported' => 0,
                'failed' => 0,
                'current_page' => 0,
                'message' => 'خطا: ' . $e->getMessage()
            ]);
            
            $this->logger->error('خطای کلی در ایمپورت', [
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * دریافت تمام محصولات از API
     */
    private function fetchAllProducts(array $config): array {
        $client = new Client([
            'base_uri' => sprintf('http://%s:%d', $config['host'], $config['port']),
            'timeout' => 20,
            'http_errors' => false,
            'headers' => ['Accept' => 'application/json'],
        ]);
        
        // Login
        $response = $client->get('/services/login/', [
            'query' => [
                'username' => $config['username'],
                'password' => $config['password'],
            ],
        ]);
        
        $login = json_decode((string) $response->getBody(), true);
        $token = $login['Result']['UserKey'] ?? $login['Message']['UserKey'] ?? null;
        
        if (!$token) {
            throw new \Exception('UserKey دریافت نشد');
        }
        
        $allProducts = [];
        $pageIndex = 1;
        $pageCount = 100;
        
        while (true) {
            update_option('zargar_import_progress', [
                'status' => 'fetching',
                'total' => count($allProducts),
                'imported' => 0,
                'failed' => 0,
                'current_page' => $pageIndex,
                'message' => sprintf('در حال دریافت صفحه %d...', $pageIndex)
            ]);
            
            $response = $client->get('/Services/MadeGold/List/', [
                'query' => [
                    'userkey' => $token,
                    'PageIndex' => $pageIndex,
                    'PageCount' => $pageCount,
                ],
            ]);
            
            $decoded = json_decode((string) $response->getBody(), true);
            
            if (($decoded['Status'] ?? null) === 'Error') {
                break;
            }
            
            if (!isset($decoded['Result']) || empty($decoded['Result'])) {
                break;
            }
            
            foreach ($decoded['Result'] as $product) {
                $allProducts[] = $product;
            }
            
            $pageIndex++;
            
            // محدودیت ایمنی (حداکثر 50 صفحه = 5000 محصول)
            if ($pageIndex > 50) {
                break;
            }
        }
        
        return $allProducts;
    }
    
    /**
     * ایمپورت یک محصول
     */
    public function importSingleProduct(array $productData, array $selectedFields): void {
        $productId = $productData['ProductId'] ?? null;
        $sku = $productData['ProductCode'] ?? null;
        
        if (!$sku) {
            throw new \Exception('محصول فاقد SKU است');
        }
        
        // پیدا کردن محصول موجود یا ایجاد جدید
        $existingProduct = wc_get_product_id_by_sku($sku);
        
        if ($existingProduct) {
            $product = wc_get_product($existingProduct);
        } else {
            $product = new \WC_Product_Simple();
            $product->set_sku($sku);
        }
        
        // اعمال فیلدهای انتخاب شده
        $mapper = new FieldMapper();
        $mapper->applyFieldsToProduct($product, $productData, $selectedFields);
        
        $product->save();
        
        $this->logger->product('محصول ایمپورت شد', [
            'product_id' => $product->get_id(),
            'sku' => $sku,
            'title' => $product->get_name()
        ]);
    }
    
    /**
     * جستجوی محصولات بر اساس کدها
     */
    public function ajaxSearchProducts(): void {
        check_ajax_referer('zargar_import_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'دسترسی غیرمجاز']);
            return;
        }
        
        $codes = isset($_POST['codes']) ? json_decode(stripslashes($_POST['codes']), true) : [];
        
        if (empty($codes)) {
            wp_send_json_error(['message' => 'کد محصول ارسال نشده است']);
            return;
        }
        
        try {
            $products = $this->searchProductsByCodes($codes);
            
            wp_send_json_success([
                'products' => $products,
                'total' => count($products)
            ]);
            
        } catch (\Exception $e) {
            $this->logger->error('خطا در جستجوی محصولات', [
                'error' => $e->getMessage()
            ]);
            wp_send_json_error(['message' => $e->getMessage()]);
        }
    }
    
    /**
     * جستجوی محصولات در API بر اساس کدها - ساده شده
     */
    public function searchProductsByCodes(array $codes): array {
        $foundProducts = [];
        $searchCodes = array_map('strtoupper', array_map('trim', $codes));
        
        // استفاده از همان کانفیگ ایمپورت عادی
        $config = [
            'host' => get_option('zargar_server_ip'),
            'port' => get_option('zargar_server_port', 8090),
            'username' => get_option('zargar_username'),
            'password' => get_option('zargar_password'),
        ];
        
        $client = new Client([
            'base_uri' => sprintf('http://%s:%d', $config['host'], $config['port']),
            'timeout' => 20,
            'http_errors' => false,
            'headers' => ['Accept' => 'application/json'],
        ]);
        
        // Login
        $response = $client->get('/services/login/', [
            'query' => [
                'username' => $config['username'],
                'password' => $config['password'],
            ],
        ]);
        
        $login = json_decode((string) $response->getBody(), true);
        $userkey = $login['Result']['UserKey'] ?? $login['Message']['UserKey'] ?? null;
        
        if (!$userkey) {
            throw new \Exception('UserKey دریافت نشد');
        }
        
        $baseUri = sprintf('http://%s:%d', $config['host'], $config['port']);
        
        // جستجو در صفحات
        $pageIndex = 1;
        $found = [];
        
        while ($pageIndex <= 50 && count($found) < count($searchCodes)) {
            $response = $client->get('/Services/MadeGold/List/', [
                'query' => [
                    'userkey' => $userkey,
                    'PageIndex' => $pageIndex,
                    'PageCount' => 100,
                ],
            ]);
            
            $decoded = json_decode((string) $response->getBody(), true);
            
            if (($decoded['Status'] ?? null) === 'Error') {
                break;
            }
            
            if (!isset($decoded['Result']) || empty($decoded['Result'])) {
                break;
            }
            
            // جستجو در این صفحه
            foreach ($decoded['Result'] as $product) {
                $productCode = strtoupper(trim($product['ProductCode'] ?? ''));
                
                if (in_array($productCode, $searchCodes) && !isset($found[$productCode])) {
                    // Fix image URLs
                    $imageFields = ['ImageURL1', 'ImageURL2', 'ImageURL3', 'ImageURL4', 'ImageURL5', 'ImageURL6', 'DefaultImageURL'];
                    foreach ($imageFields as $field) {
                        if (!empty($product[$field])) {
                            $product[$field] = rtrim($baseUri, '/') . '/' . ltrim($product[$field], '/');
                        }
                    }
                    
                    $foundProducts[] = $product;
                    $found[$productCode] = true;
                }
            }
            
            if (count($found) >= count($searchCodes)) {
                break;
            }
            
            $pageIndex++;
        }
        
        return $foundProducts;
    }
    
    /**
     * ایمپورت محصولات خاص
     */
    public function ajaxImportSpecificProducts(): void {
        check_ajax_referer('zargar_import_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'دسترسی غیرمجاز']);
            return;
        }
        
        $codes = isset($_POST['codes']) ? json_decode(stripslashes($_POST['codes']), true) : [];
        $selectedFields = isset($_POST['fields']) ? json_decode(stripslashes($_POST['fields']), true) : [];
        
        if (empty($codes) || empty($selectedFields)) {
            wp_send_json_error(['message' => 'اطلاعات ناقص است']);
            return;
        }
        
        try {
            // Initialize progress
            update_option('zargar_import_progress', [
                'status' => 'running',
                'total' => count($codes),
                'imported' => 0,
                'failed' => 0,
                'message' => 'در حال شروع...'
            ]);
            
            // جستجوی محصولات
            $products = $this->searchProductsByCodes($codes);
            
            if (empty($products)) {
                wp_send_json_error(['message' => 'هیچ محصولی یافت نشد']);
                return;
            }
            
            // ایمپورت محصولات
            $imported = 0;
            $failed = 0;
            
            foreach ($products as $productData) {
                try {
                    update_option('zargar_import_progress', [
                        'status' => 'running',
                        'total' => count($codes),
                        'imported' => $imported,
                        'failed' => $failed,
                        'message' => 'در حال ایمپورت ' . ($productData['ProductCode'] ?? '')
                    ]);
                    
                    $this->importSingleProduct($productData, $selectedFields);
                    $imported++;
                    
                } catch (\Exception $e) {
                    $failed++;
                    $this->logger->error('خطا در ایمپورت محصول', [
                        'product' => $productData['ProductCode'] ?? 'Unknown',
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            // Complete
            update_option('zargar_import_progress', [
                'status' => 'completed',
                'total' => count($codes),
                'imported' => $imported,
                'failed' => $failed,
                'message' => 'ایمپورت با موفقیت تکمیل شد'
            ]);
            
            wp_send_json_success([
                'message' => "ایمپورت تکمیل شد: {$imported} موفق، {$failed} ناموفق",
                'imported' => $imported,
                'failed' => $failed
            ]);
            
        } catch (\Exception $e) {
            update_option('zargar_import_progress', [
                'status' => 'error',
                'total' => 0,
                'imported' => 0,
                'failed' => 0,
                'message' => 'خطا: ' . $e->getMessage()
            ]);
            
            $this->logger->error('خطا در ایمپورت محصولات خاص', [
                'error' => $e->getMessage()
            ]);
            wp_send_json_error(['message' => $e->getMessage()]);
        }
    }
    
}
