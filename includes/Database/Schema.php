<?php
/**
 * Database Schema Manager
 * 
 * Manages custom database tables for Zargar Accounting
 * 
 * @package ZargarAccounting
 * @since 2.0.0
 */

namespace ZargarAccounting\Database;

use ZargarAccounting\Logger\MonologManager;

if (!defined('ABSPATH')) {
    exit;
}

class Schema {
    private static $instance = null;
    private $logger;
    private $table_name;
    private $charset_collate;
    
    private function __construct() {
        global $wpdb;
        $this->logger = MonologManager::getInstance();
        $this->table_name = $wpdb->prefix . 'zargar_products';
        $this->charset_collate = $wpdb->get_charset_collate();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Get table name
     */
    public function getTableName(): string {
        return $this->table_name;
    }
    
    /**
     * Create tables
     */
    public function createTables(): bool {
        global $wpdb;
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
        $sql = "CREATE TABLE {$this->table_name} (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            product_id bigint(20) UNSIGNED NOT NULL,
            location varchar(255) DEFAULT NULL,
            external_id varchar(100) DEFAULT NULL,
            stone_price decimal(15,2) DEFAULT 0.00,
            wage_price decimal(15,2) DEFAULT 0.00,
            income_total decimal(15,2) DEFAULT 0.00,
            tax_total decimal(15,2) DEFAULT 0.00,
            sale_wage_percent decimal(5,2) DEFAULT 0.00,
            sale_wage_price decimal(15,2) DEFAULT 0.00,
            sale_wage_price_type varchar(50) DEFAULT NULL,
            sale_wage_stone decimal(15,2) DEFAULT 0.00,
            office_code varchar(100) DEFAULT NULL,
            designer_code varchar(100) DEFAULT NULL,
            extra_field_1 text DEFAULT NULL,
            extra_field_2 text DEFAULT NULL,
            synced_at datetime DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY product_id (product_id),
            KEY external_id (external_id),
            KEY stone_price (stone_price),
            KEY wage_price (wage_price),
            KEY office_code (office_code),
            KEY designer_code (designer_code),
            KEY synced_at (synced_at)
        ) {$this->charset_collate};";
        
        dbDelta($sql);
        
        // Check if table was created
        if ($wpdb->get_var("SHOW TABLES LIKE '{$this->table_name}'") === $this->table_name) {
            $this->logger->product('جدول دیتابیس زرگر ایجاد شد', [
                'table_name' => $this->table_name
            ]);
            
            update_option('zargar_db_version', '1.0.0');
            return true;
        }
        
        $this->logger->error('خطا در ایجاد جدول دیتابیس', [
            'table_name' => $this->table_name,
            'error' => $wpdb->last_error
        ]);
        
        return false;
    }
    
    /**
     * Drop tables
     */
    public function dropTables(): bool {
        global $wpdb;
        
        $result = $wpdb->query("DROP TABLE IF EXISTS {$this->table_name}");
        
        if ($result !== false) {
            $this->logger->product('جدول دیتابیس زرگر حذف شد', [
                'table_name' => $this->table_name
            ]);
            
            delete_option('zargar_db_version');
            return true;
        }
        
        return false;
    }
    
    /**
     * Check if tables exist
     */
    public function tablesExist(): bool {
        global $wpdb;
        return $wpdb->get_var("SHOW TABLES LIKE '{$this->table_name}'") === $this->table_name;
    }
    
    /**
     * Migrate data from postmeta to custom table
     */
    public function migrateFromPostMeta(): array {
        global $wpdb;
        
        $migrated = 0;
        $errors = 0;
        
        // Get all products
        $products = get_posts([
            'post_type' => 'product',
            'posts_per_page' => -1,
            'post_status' => 'any'
        ]);
        
        foreach ($products as $product) {
            try {
                $data = [
                    'product_id' => $product->ID,
                    'location' => get_post_meta($product->ID, '_location', true),
                    'external_id' => get_post_meta($product->ID, '_external_id', true),
                    'stone_price' => get_post_meta($product->ID, '_stone_price', true) ?: 0,
                    'wage_price' => get_post_meta($product->ID, 'wage_price', true) ?: 0,
                    'income_total' => get_post_meta($product->ID, '_income_total', true) ?: 0,
                    'tax_total' => get_post_meta($product->ID, '_tax_total', true) ?: 0,
                    'sale_wage_percent' => get_post_meta($product->ID, 'sale_wage_percent', true) ?: 0,
                    'sale_wage_price' => get_post_meta($product->ID, 'sale_wage_price', true) ?: 0,
                    'sale_wage_price_type' => get_post_meta($product->ID, 'sale_wage_price_type', true),
                    'sale_wage_stone' => get_post_meta($product->ID, 'sale_wage_stone', true) ?: 0,
                    'office_code' => get_post_meta($product->ID, '_office_code', true),
                    'designer_code' => get_post_meta($product->ID, '_designer_code', true),
                    'extra_field_1' => get_post_meta($product->ID, '_extra_field_1', true),
                    'extra_field_2' => get_post_meta($product->ID, '_extra_field_2', true),
                ];
                
                // Check if any data exists
                $has_data = false;
                foreach ($data as $key => $value) {
                    if ($key !== 'product_id' && !empty($value)) {
                        $has_data = true;
                        break;
                    }
                }
                
                if ($has_data) {
                    $result = $wpdb->replace($this->table_name, $data);
                    if ($result !== false) {
                        $migrated++;
                    } else {
                        $errors++;
                    }
                }
            } catch (\Exception $e) {
                $errors++;
                $this->logger->error('خطا در migrate محصول', [
                    'product_id' => $product->ID,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        $this->logger->product('Migration از postmeta به جدول اختصاصی', [
            'migrated' => $migrated,
            'errors' => $errors,
            'total_products' => count($products)
        ]);
        
        return [
            'success' => true,
            'migrated' => $migrated,
            'errors' => $errors,
            'total' => count($products)
        ];
    }
}
