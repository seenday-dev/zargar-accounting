<?php
/**
 * Assets Manager - Handle CSS and JS
 * 
 * @package ZargarAccounting
 * @since 2.0.0
 */

namespace ZargarAccounting\Admin;

if (!defined('ABSPATH')) {
    exit;
}

class AssetsManager {
    private static $instance = null;
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function registerHooks() {
        add_action('admin_enqueue_scripts', [$this, 'enqueueScripts']);
    }
    
    public function enqueueScripts($hook) {
        // Only load on our plugin pages
        if (strpos($hook, 'zargar-accounting') === false) {
            return;
        }
        
        // Enqueue WordPress dashicons (needed for dashboard page icons)
        wp_enqueue_style('dashicons');
        
        // Enqueue LineIcons (local - fixed version)
        wp_enqueue_style(
            'zargar-lineicons',
            ZARGAR_ACCOUNTING_PLUGIN_URL . 'assets/icons/lineicons.css',
            [],
            ZARGAR_ACCOUNTING_VERSION
        );
        
        // Enqueue main CSS for all plugin pages
        wp_enqueue_style(
            'zargar-main',
            ZARGAR_ACCOUNTING_PLUGIN_URL . 'assets/css/main.css',
            ['dashicons', 'zargar-lineicons'],
            ZARGAR_ACCOUNTING_VERSION
        );
        
        wp_enqueue_style(
            'zargar-sidebar',
            ZARGAR_ACCOUNTING_PLUGIN_URL . 'assets/css/sidebar.css',
            [],
            ZARGAR_ACCOUNTING_VERSION
        );
        
        // Dashboard page
        if ($hook === 'toplevel_page_zargar-accounting') {
            wp_enqueue_style(
                'zargar-dashboard',
                ZARGAR_ACCOUNTING_PLUGIN_URL . 'assets/css/dashboard.css',
                ['zargar-main'],
                ZARGAR_ACCOUNTING_VERSION
            );
        }
        
        // Logs page - NO AJAX, just simple PHP
        // We don't need logs.js anymore, it's purely server-side rendered
    }
}
