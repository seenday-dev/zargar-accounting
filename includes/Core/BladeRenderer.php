<?php
/**
 * Blade Template Renderer
 * 
 * @package ZargarAccounting
 * @since 1.0.0
 */

namespace ZargarAccounting\Core;

use Jenssegers\Blade\Blade;

if (!defined('ABSPATH')) {
    exit;
}

class BladeRenderer {
    private static $instance = null;
    private $blade;
    
    private function __construct() {
        // Determine plugin directory
        if (defined('ZARGAR_ACCOUNTING_PLUGIN_DIR')) {
            $plugin_dir = ZARGAR_ACCOUNTING_PLUGIN_DIR;
        } else {
            // Fallback: calculate from current file location
            $plugin_dir = dirname(dirname(__DIR__)) . '/';
        }
        
        $views_path = $plugin_dir . 'templates';
        $cache_path = $plugin_dir . 'storage/cache';
        
        // Ensure cache directory exists
        if (!file_exists($cache_path)) {
            if (function_exists('wp_mkdir_p')) {
                wp_mkdir_p($cache_path);
            } else {
                mkdir($cache_path, 0755, true);
            }
        }
        
        $this->blade = new Blade($views_path, $cache_path);
        
        // Register custom directives
        $this->registerDirectives();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function registerDirectives() {
        // Add WordPress-specific directives
        
        // @nonce directive
        $this->blade->directive('nonce', function ($field) {
            return "<?php wp_nonce_field({$field}); ?>";
        });
        
        // @can directive for capabilities
        $this->blade->directive('can', function ($capability) {
            return "<?php if (current_user_can({$capability})): ?>";
        });
        
        $this->blade->directive('endcan', function () {
            return "<?php endif; ?>";
        });
    }
    
    public function render($view, $data = []) {
        try {
            // Add common data available to all views
            $current_user = function_exists('wp_get_current_user') ? wp_get_current_user() : (object)['display_name' => 'Guest'];
            
            $data = array_merge([
                'plugin_url' => ZARGAR_ACCOUNTING_PLUGIN_URL,
                'plugin_version' => ZARGAR_ACCOUNTING_VERSION,
                'current_user' => $current_user,
            ], $data);
            
            return $this->blade->render($view, $data);
        } catch (\Exception $e) {
            $logger = \ZargarAccounting\Logger\Logger::getInstance();
            $logger->error('Blade rendering error: ' . $e->getMessage(), [
                'view' => $view,
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            if (defined('WP_DEBUG') && WP_DEBUG) {
                return '<div style="background: #f8d7da; color: #721c24; padding: 15px; border: 1px solid #f5c6cb; border-radius: 4px;">' .
                       '<strong>Blade Rendering Error:</strong> ' . esc_html($e->getMessage()) .
                       '</div>';
            }
            
            return '<div class="notice notice-error"><p>Template rendering failed.</p></div>';
        }
    }
}
