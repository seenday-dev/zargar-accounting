<?php
/**
 * Admin Menu Manager
 * 
 * @package ZargarAccounting
 * @since 2.0.0
 */

namespace ZargarAccounting\Admin;

use ZargarAccounting\Core\BladeRenderer;

if (!defined('ABSPATH')) {
    exit;
}

class MenuManager {
    private static $instance = null;
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function registerHooks() {
        add_action('admin_menu', [$this, 'addMenuPages']);
    }
    
    public function addMenuPages() {
        // Main menu
        add_menu_page(
            __('حسابداری زرگر', 'zargar-accounting'),
            __('حسابداری زرگر', 'zargar-accounting'),
            'manage_options',
            'zargar-accounting',
            [$this, 'renderDashboard'],
            'dashicons-calculator',
            30
        );
        
        // Dashboard submenu
        add_submenu_page(
            'zargar-accounting',
            __('داشبورد', 'zargar-accounting'),
            __('داشبورد', 'zargar-accounting'),
            'manage_options',
            'zargar-accounting',
            [$this, 'renderDashboard']
        );
        
        // Settings submenu
        add_submenu_page(
            'zargar-accounting',
            __('تنظیمات', 'zargar-accounting'),
            __('تنظیمات', 'zargar-accounting'),
            'manage_options',
            'zargar-accounting-settings',
            [$this, 'renderSettings']
        );
        
        // Logs submenu
        add_submenu_page(
            'zargar-accounting',
            __('گزارش‌ها', 'zargar-accounting'),
            __('گزارش‌ها', 'zargar-accounting'),
            'manage_options',
            'zargar-accounting-logs',
            [$this, 'renderLogs']
        );
    }
    
    public function renderDashboard() {
        $blade = BladeRenderer::getInstance();
        echo $blade->render('admin.dashboard', [
            'title' => 'داشبورد'
        ]);
    }
    
    public function renderSettings() {
        $blade = BladeRenderer::getInstance();
        echo $blade->render('admin.settings', [
            'title' => 'تنظیمات',
            'server_ip' => get_option('zargar_server_ip', ''),
            'server_port' => get_option('zargar_server_port', '8080'),
            'username' => get_option('zargar_username', ''),
            'password' => get_option('zargar_password', '')
        ]);
    }
    
    public function renderLogs() {
        // Use new simple PHP viewer instead of Blade + AJAX
        $viewer = LogsViewer::getInstance();
        $viewer->render();
    }
}
