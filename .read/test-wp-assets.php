<?php
/**
 * تست مسیرهای فایل‌ها در وردپرس
 * این فایل رو در روت وردپرس قرار بده و اجرا کن
 */

// Load WordPress
require_once('wp-load.php');

// شبیه‌سازی محیط Admin
set_current_screen('zargar-accounting');

echo "<h2>🔍 تست مسیرهای Assets</h2>";

// فعال کردن افزونه اگر غیرفعال است
$plugin_file = 'zargar-accounting/zargar-accounting.php';
if (!is_plugin_active($plugin_file)) {
    echo "<p style='color:orange'>⚠️ افزونه غیرفعال است!</p>";
}

echo "<h3>مسیرها:</h3>";
echo "<pre>";
echo "ZARGAR_ACCOUNTING_PLUGIN_URL: " . (defined('ZARGAR_ACCOUNTING_PLUGIN_URL') ? ZARGAR_ACCOUNTING_PLUGIN_URL : 'NOT DEFINED') . "\n";
echo "ZARGAR_ACCOUNTING_PLUGIN_DIR: " . (defined('ZARGAR_ACCOUNTING_PLUGIN_DIR') ? ZARGAR_ACCOUNTING_PLUGIN_DIR : 'NOT DEFINED') . "\n";
echo "</pre>";

// بررسی فایل‌های CSS
$css_files = [
    'lineicons-fixed.css' => ZARGAR_ACCOUNTING_PLUGIN_URL . 'assets/icons/lineicons-fixed.css',
    'main.css' => ZARGAR_ACCOUNTING_PLUGIN_URL . 'assets/css/main.css',
    'sidebar.css' => ZARGAR_ACCOUNTING_PLUGIN_URL . 'assets/css/sidebar.css',
];

echo "<h3>فایل‌های CSS:</h3>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>فایل</th><th>URL</th><th>وضعیت</th></tr>";

foreach ($css_files as $name => $url) {
    $path = str_replace(ZARGAR_ACCOUNTING_PLUGIN_URL, ZARGAR_ACCOUNTING_PLUGIN_DIR, $url);
    $exists = file_exists($path);
    $status = $exists ? '✅ موجود' : '❌ ناموجود';
    $color = $exists ? 'green' : 'red';
    
    echo "<tr>";
    echo "<td>{$name}</td>";
    echo "<td><a href='{$url}' target='_blank'>{$url}</a></td>";
    echo "<td style='color:{$color}'>{$status}</td>";
    echo "</tr>";
}

echo "</table>";

// بررسی فایل‌های فونت
$font_files = [
    'LineIcons.woff2' => ZARGAR_ACCOUNTING_PLUGIN_URL . 'assets/icons/LineIcons.woff2',
    'LineIcons.woff' => ZARGAR_ACCOUNTING_PLUGIN_URL . 'assets/icons/LineIcons.woff',
];

echo "<h3>فایل‌های فونت:</h3>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>فایل</th><th>URL</th><th>وضعیت</th><th>حجم</th></tr>";

foreach ($font_files as $name => $url) {
    $path = str_replace(ZARGAR_ACCOUNTING_PLUGIN_URL, ZARGAR_ACCOUNTING_PLUGIN_DIR, $url);
    $exists = file_exists($path);
    $status = $exists ? '✅ موجود' : '❌ ناموجود';
    $color = $exists ? 'green' : 'red';
    $size = $exists ? size_format(filesize($path)) : '-';
    
    echo "<tr>";
    echo "<td>{$name}</td>";
    echo "<td><a href='{$url}' target='_blank'>{$url}</a></td>";
    echo "<td style='color:{$color}'>{$status}</td>";
    echo "<td>{$size}</td>";
    echo "</tr>";
}

echo "</table>";

// تست دسترسی HTTP
echo "<h3>تست دسترسی HTTP:</h3>";
echo "<iframe src='" . ZARGAR_ACCOUNTING_PLUGIN_URL . "assets/icons/lineicons-fixed.css' width='100%' height='200'></iframe>";

echo "<h3>پیش‌نمایش آیکون‌ها:</h3>";
echo "<link rel='stylesheet' href='" . ZARGAR_ACCOUNTING_PLUGIN_URL . "assets/icons/lineicons-fixed.css'>";
echo "<div style='font-size: 48px; padding: 20px; background: #f5f5f5;'>";
echo "<i class='lni lni-home'></i> ";
echo "<i class='lni lni-cog'></i> ";
echo "<i class='lni lni-list'></i> ";
echo "<i class='lni lni-stats-up'></i> ";
echo "</div>";

echo "<h3>بررسی AssetsManager:</h3>";
if (class_exists('ZargarAccounting\Admin\AssetsManager')) {
    echo "<p style='color:green'>✅ کلاس AssetsManager وجود دارد</p>";
    
    // شبیه‌سازی enqueue_scripts
    ob_start();
    do_action('admin_enqueue_scripts', 'toplevel_page_zargar-accounting');
    ob_end_clean();
    
    global $wp_styles;
    echo "<h4>استایل‌های بارگذاری شده:</h4>";
    echo "<pre>";
    foreach ($wp_styles->queue as $handle) {
        if (strpos($handle, 'zargar') !== false) {
            $style = $wp_styles->registered[$handle];
            echo "Handle: {$handle}\n";
            echo "  URL: {$style->src}\n";
            echo "  Deps: " . implode(', ', $style->deps) . "\n\n";
        }
    }
    echo "</pre>";
} else {
    echo "<p style='color:red'>❌ کلاس AssetsManager وجود ندارد!</p>";
}
?>
