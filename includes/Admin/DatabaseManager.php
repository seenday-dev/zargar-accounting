<?php
/**
 * Database Manager
 * 
 * Handles database operations and migrations
 * 
 * @package ZargarAccounting
 * @since 2.0.0
 */

namespace ZargarAccounting\Admin;

use ZargarAccounting\Database\Schema;
use ZargarAccounting\Logger\MonologManager;

if (!defined('ABSPATH')) {
    exit;
}

class DatabaseManager {
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
        add_action('wp_ajax_zargar_create_tables', [$this, 'ajaxCreateTables']);
        add_action('wp_ajax_zargar_migrate_data', [$this, 'ajaxMigrateData']);
        add_action('wp_ajax_zargar_drop_tables', [$this, 'ajaxDropTables']);
    }
    
    /**
     * AJAX: Create database tables
     */
    public function ajaxCreateTables(): void {
        if (!check_ajax_referer('zargar_ajax_nonce', 'nonce', false)) {
            wp_send_json_error(['message' => 'عدم دسترسی']);
            return;
        }
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'شما دسترسی لازم را ندارید']);
            return;
        }
        
        $schema = Schema::getInstance();
        
        if ($schema->tablesExist()) {
            wp_send_json_error(['message' => 'جداول قبلاً ایجاد شده‌اند']);
            return;
        }
        
        $created = $schema->createTables();
        
        if ($created) {
            wp_send_json_success([
                'message' => 'جداول با موفقیت ایجاد شدند',
                'table_name' => $schema->getTableName()
            ]);
        } else {
            wp_send_json_error(['message' => 'خطا در ایجاد جداول']);
        }
    }
    
    /**
     * AJAX: Migrate data from postmeta
     */
    public function ajaxMigrateData(): void {
        if (!check_ajax_referer('zargar_ajax_nonce', 'nonce', false)) {
            wp_send_json_error(['message' => 'عدم دسترسی']);
            return;
        }
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'شما دسترسی لازم را ندارید']);
            return;
        }
        
        $schema = Schema::getInstance();
        
        if (!$schema->tablesExist()) {
            wp_send_json_error(['message' => 'ابتدا جداول را ایجاد کنید']);
            return;
        }
        
        $result = $schema->migrateFromPostMeta();
        
        wp_send_json_success([
            'message' => sprintf(
                '%d محصول migrate شد، %d خطا، از مجموع %d محصول',
                $result['migrated'],
                $result['errors'],
                $result['total']
            ),
            'stats' => $result
        ]);
    }
    
    /**
     * AJAX: Drop tables
     */
    public function ajaxDropTables(): void {
        if (!check_ajax_referer('zargar_ajax_nonce', 'nonce', false)) {
            wp_send_json_error(['message' => 'عدم دسترسی']);
            return;
        }
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'شما دسترسی لازم را ندارید']);
            return;
        }
        
        $schema = Schema::getInstance();
        $dropped = $schema->dropTables();
        
        if ($dropped) {
            wp_send_json_success(['message' => 'جداول با موفقیت حذف شدند']);
        } else {
            wp_send_json_error(['message' => 'خطا در حذف جداول']);
        }
    }
}
