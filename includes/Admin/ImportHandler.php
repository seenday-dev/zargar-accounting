<?php
/**
 * Import Handler
 * 
 * Handles importing metadata fields and WooCommerce attributes
 * 
 * @package ZargarAccounting
 * @since 2.0.0
 */

namespace ZargarAccounting\Admin;

use ZargarAccounting\Logger\MonologManager;

if (!defined('ABSPATH')) {
    exit;
}

class ImportHandler {
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
        add_action('wp_ajax_zargar_import_metadata', [$this, 'ajaxImportMetadata']);
        add_action('wp_ajax_zargar_import_attributes', [$this, 'ajaxImportAttributes']);
    }
    
    /**
     * AJAX handler for importing metadata
     */
    public function ajaxImportMetadata(): void {
        // Verify nonce
        if (!check_ajax_referer('zargar_import_nonce', 'nonce', false)) {
            wp_send_json_error([
                'message' => 'عدم دسترسی - نشانه امنیتی نامعتبر است'
            ]);
            return;
        }
        
        // Check capability
        if (!current_user_can('manage_options')) {
            wp_send_json_error([
                'message' => 'شما دسترسی لازم را ندارید'
            ]);
            return;
        }
        
        // Ensure database tables exist - create if not
        $schema = \ZargarAccounting\Database\Schema::getInstance();
        
        $this->logger->product('بررسی وجود جداول دیتابیس');
        
        if (!$schema->tablesExist()) {
            $this->logger->product('جداول وجود ندارند - در حال ایجاد...');
            
            $created = $schema->createTables();
            
            if (!$created) {
                $this->logger->error('خطا در ایجاد جداول دیتابیس');
                wp_send_json_error([
                    'message' => 'خطا در ایجاد جداول دیتابیس. لطفاً دسترسی‌های دیتابیس را بررسی کنید.'
                ]);
                return;
            }
            
            $this->logger->product('جداول دیتابیس با موفقیت ایجاد شدند');
        } else {
            $this->logger->product('جداول دیتابیس موجود هستند');
        }
        
        $metaFields = [
            ['meta_key' => '_location', 'label' => 'محل نگهداری'],
            ['meta_key' => '_external_id', 'label' => 'شناسه خارجی'],
            ['meta_key' => '_stone_price', 'label' => 'قیمت سنگ'],
            ['meta_key' => 'wage_price', 'label' => 'قیمت اجرت'],
            ['meta_key' => '_income_total', 'label' => 'جمع درآمد'],
            ['meta_key' => '_tax_total', 'label' => 'جمع مالیات'],
            ['meta_key' => 'sale_wage_percent', 'label' => 'درصد اجرت فروش'],
            ['meta_key' => 'sale_wage_price', 'label' => 'مبلغ اجرت فروش'],
            ['meta_key' => 'sale_wage_price_type', 'label' => 'نوع اجرت فروش'],
            ['meta_key' => 'sale_wage_stone', 'label' => 'اجرت سنگ فروش'],
            ['meta_key' => '_office_code', 'label' => 'کد دفتر'],
            ['meta_key' => '_designer_code', 'label' => 'کد طراح'],
            ['meta_key' => '_extra_field_1', 'label' => 'فیلد اضافه ۱'],
            ['meta_key' => '_extra_field_2', 'label' => 'فیلد اضافه ۲'],
        ];
        
        $registered = [];
        
        foreach ($metaFields as $meta) {
            register_post_meta('product', $meta['meta_key'], [
                'type' => 'string',
                'description' => $meta['label'],
                'single' => true,
                'show_in_rest' => true,
            ]);
            
            $registered[] = [
                'key' => $meta['meta_key'],
                'label' => $meta['label']
            ];
        }
        
        $this->logger->product('متادیتاهای محصول ثبت شدند', [
            'count' => count($registered),
            'fields' => array_column($registered, 'label')
        ]);
        
        wp_send_json_success([
            'message' => sprintf('%d متادیتا با موفقیت ثبت شد', count($registered)),
            'fields' => $registered,
            'count' => count($registered)
        ]);
    }
    
    /**
     * AJAX handler for importing WooCommerce attributes
     */
    public function ajaxImportAttributes(): void {
        // Verify nonce
        if (!check_ajax_referer('zargar_import_nonce', 'nonce', false)) {
            wp_send_json_error([
                'message' => 'عدم دسترسی - نشانه امنیتی نامعتبر است'
            ]);
            return;
        }
        
        // Check capability
        if (!current_user_can('manage_options')) {
            wp_send_json_error([
                'message' => 'شما دسترسی لازم را ندارید'
            ]);
            return;
        }
        
        // Check WooCommerce
        if (!function_exists('wc_create_attribute')) {
            wp_send_json_error([
                'message' => 'WooCommerce فعال نیست. لطفاً ابتدا افزونه WooCommerce را نصب و فعال کنید.'
            ]);
            return;
        }
        
        $attributes = [
            ['slug' => 'base_weight', 'label' => 'وزن پایه'],
            ['slug' => 'weight', 'label' => 'وزن'],
            ['slug' => 'model', 'label' => 'مدل'],
            ['slug' => 'collection', 'label' => 'مجموعه'],
            ['slug' => 'color', 'label' => 'رنگ'],
            ['slug' => 'size', 'label' => 'سایز'],
            ['slug' => '_weight_symbol_rate', 'label' => 'نرخ وزن'],
        ];
        
        $existing = wc_get_attribute_taxonomies();
        $existingSlugs = array_map(function($tax) {
            return $tax->attribute_name;
        }, $existing);
        
        $created = [];
        $skipped = [];
        $errors = [];
        
        foreach ($attributes as $attr) {
            if (in_array($attr['slug'], $existingSlugs, true)) {
                $skipped[] = [
                    'slug' => $attr['slug'],
                    'label' => $attr['label'],
                    'reason' => 'قبلاً ایجاد شده'
                ];
                continue;
            }
            
            $result = wc_create_attribute([
                'slug' => $attr['slug'],
                'name' => $attr['label'],
                'type' => 'select',
                'order_by' => 'menu_order',
                'has_archives' => false,
            ]);
            
            if (is_wp_error($result)) {
                $errors[] = [
                    'label' => $attr['label'],
                    'error' => $result->get_error_message()
                ];
            } else {
                $created[] = [
                    'slug' => $attr['slug'],
                    'label' => $attr['label']
                ];
            }
        }
        
        $this->logger->product('ویژگی‌های محصول پردازش شدند', [
            'created' => count($created),
            'skipped' => count($skipped),
            'errors' => count($errors)
        ]);
        
        // Determine success or partial success
        if (count($errors) > 0 && count($created) === 0) {
            wp_send_json_error([
                'message' => 'خطا در ایجاد ویژگی‌ها',
                'errors' => $errors
            ]);
        } else {
            $message = sprintf(
                '%d ویژگی ایجاد شد، %d قبلاً وجود داشت',
                count($created),
                count($skipped)
            );
            
            if (count($errors) > 0) {
                $message .= sprintf('، %d خطا', count($errors));
            }
            
            wp_send_json_success([
                'message' => $message,
                'created' => $created,
                'skipped' => $skipped,
                'errors' => $errors,
                'counts' => [
                    'created' => count($created),
                    'skipped' => count($skipped),
                    'errors' => count($errors)
                ]
            ]);
        }
    }
}
