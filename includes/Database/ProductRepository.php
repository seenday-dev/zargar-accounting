<?php
/**
 * Product Repository
 * 
 * Handles all database operations for Zargar product data
 * 
 * @package ZargarAccounting
 * @since 2.0.0
 */

namespace ZargarAccounting\Database;

use ZargarAccounting\Logger\MonologManager;

if (!defined('ABSPATH')) {
    exit;
}

class ProductRepository {
    private static $instance = null;
    private $logger;
    private $table_name;
    
    private function __construct() {
        global $wpdb;
        $this->logger = MonologManager::getInstance();
        $this->table_name = $wpdb->prefix . 'zargar_products';
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Get product data by product ID
     */
    public function getByProductId(int $product_id): ?array {
        global $wpdb;
        
        $data = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$this->table_name} WHERE product_id = %d", $product_id),
            ARRAY_A
        );
        
        return $data ?: null;
    }
    
    /**
     * Get product data by external ID
     */
    public function getByExternalId(string $external_id): ?array {
        global $wpdb;
        
        $data = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$this->table_name} WHERE external_id = %s", $external_id),
            ARRAY_A
        );
        
        return $data ?: null;
    }
    
    /**
     * Create or update product data
     */
    public function save(int $product_id, array $data): bool {
        global $wpdb;
        
        // Prepare data
        $save_data = [
            'product_id' => $product_id,
            'location' => $data['location'] ?? null,
            'external_id' => $data['external_id'] ?? null,
            'stone_price' => $data['stone_price'] ?? 0,
            'wage_price' => $data['wage_price'] ?? 0,
            'income_total' => $data['income_total'] ?? 0,
            'tax_total' => $data['tax_total'] ?? 0,
            'sale_wage_percent' => $data['sale_wage_percent'] ?? 0,
            'sale_wage_price' => $data['sale_wage_price'] ?? 0,
            'sale_wage_price_type' => $data['sale_wage_price_type'] ?? null,
            'sale_wage_stone' => $data['sale_wage_stone'] ?? 0,
            'office_code' => $data['office_code'] ?? null,
            'designer_code' => $data['designer_code'] ?? null,
            'extra_field_1' => $data['extra_field_1'] ?? null,
            'extra_field_2' => $data['extra_field_2'] ?? null,
        ];
        
        // Check if exists
        $exists = $this->getByProductId($product_id);
        
        if ($exists) {
            // Update
            $result = $wpdb->update(
                $this->table_name,
                $save_data,
                ['product_id' => $product_id],
                [
                    '%d', // product_id
                    '%s', '%s', // location, external_id
                    '%f', '%f', '%f', '%f', '%f', '%f', // prices
                    '%s', '%f', // sale_wage_price_type, sale_wage_stone
                    '%s', '%s', '%s', '%s' // codes and extras
                ],
                ['%d']
            );
        } else {
            // Insert
            $result = $wpdb->insert(
                $this->table_name,
                $save_data,
                [
                    '%d', // product_id
                    '%s', '%s', // location, external_id
                    '%f', '%f', '%f', '%f', '%f', '%f', // prices
                    '%s', '%f', // sale_wage_price_type, sale_wage_stone
                    '%s', '%s', '%s', '%s' // codes and extras
                ]
            );
        }
        
        if ($result === false) {
            $this->logger->error('خطا در ذخیره داده محصول', [
                'product_id' => $product_id,
                'error' => $wpdb->last_error
            ]);
            return false;
        }
        
        return true;
    }
    
    /**
     * Delete product data
     */
    public function delete(int $product_id): bool {
        global $wpdb;
        
        $result = $wpdb->delete(
            $this->table_name,
            ['product_id' => $product_id],
            ['%d']
        );
        
        return $result !== false;
    }
    
    /**
     * Get all products with zargar data
     */
    public function getAll(array $args = []): array {
        global $wpdb;
        
        $limit = $args['limit'] ?? 100;
        $offset = $args['offset'] ?? 0;
        $order_by = $args['order_by'] ?? 'updated_at';
        $order = $args['order'] ?? 'DESC';
        
        $sql = $wpdb->prepare(
            "SELECT * FROM {$this->table_name} ORDER BY {$order_by} {$order} LIMIT %d OFFSET %d",
            $limit,
            $offset
        );
        
        return $wpdb->get_results($sql, ARRAY_A) ?: [];
    }
    
    /**
     * Count total products
     */
    public function count(): int {
        global $wpdb;
        return (int) $wpdb->get_var("SELECT COUNT(*) FROM {$this->table_name}");
    }
    
    /**
     * Search products
     */
    public function search(string $query, array $fields = []): array {
        global $wpdb;
        
        if (empty($fields)) {
            $fields = ['external_id', 'location', 'office_code', 'designer_code'];
        }
        
        $where_clauses = [];
        foreach ($fields as $field) {
            $where_clauses[] = $wpdb->prepare("{$field} LIKE %s", '%' . $wpdb->esc_like($query) . '%');
        }
        
        $where = implode(' OR ', $where_clauses);
        
        $sql = "SELECT * FROM {$this->table_name} WHERE {$where} ORDER BY updated_at DESC LIMIT 50";
        
        return $wpdb->get_results($sql, ARRAY_A) ?: [];
    }
    
    /**
     * Update synced timestamp
     */
    public function updateSyncedAt(int $product_id): bool {
        global $wpdb;
        
        return $wpdb->update(
            $this->table_name,
            ['synced_at' => current_time('mysql')],
            ['product_id' => $product_id],
            ['%s'],
            ['%d']
        ) !== false;
    }
    
    /**
     * Get products that need sync (not synced or old sync)
     */
    public function getNeedSync(int $hours = 24): array {
        global $wpdb;
        
        $since = date('Y-m-d H:i:s', strtotime("-{$hours} hours"));
        
        $sql = $wpdb->prepare(
            "SELECT * FROM {$this->table_name} 
             WHERE synced_at IS NULL OR synced_at < %s 
             ORDER BY synced_at ASC 
             LIMIT 100",
            $since
        );
        
        return $wpdb->get_results($sql, ARRAY_A) ?: [];
    }
    
    /**
     * Bulk insert/update
     */
    public function bulkSave(array $products): array {
        $success = 0;
        $errors = 0;
        
        foreach ($products as $product_id => $data) {
            if ($this->save($product_id, $data)) {
                $success++;
            } else {
                $errors++;
            }
        }
        
        return [
            'success' => $success,
            'errors' => $errors,
            'total' => count($products)
        ];
    }
    
    /**
     * Calculate income and tax (example business logic)
     */
    public function calculateTotals(array $data): array {
        // Income = stone_price + wage_price + sale_wage_price
        $income = ($data['stone_price'] ?? 0) + 
                  ($data['wage_price'] ?? 0) + 
                  ($data['sale_wage_price'] ?? 0);
        
        // Tax = 9% of income (example)
        $tax = $income * 0.09;
        
        return [
            'income_total' => round($income, 2),
            'tax_total' => round($tax, 2)
        ];
    }
}
