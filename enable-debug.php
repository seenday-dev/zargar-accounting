<?php
/**
 * WordPress Debug Enabler
 * این فایل را قبل از wp-config.php اضافه کنید یا از مرورگر اجرا کنید
 */

// دستورالعمل استفاده:
// 
// روش 1: اضافه کردن به wp-config.php
// این خطوط را قبل از "/* That's all, stop editing! */" در wp-config.php اضافه کنید:
?>

<!-- این کدها را به wp-config.php اضافه کنید -->
<pre>
// Enable WordPress Debug Mode
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', true);
@ini_set('display_errors', 1);

// Optional: Save errors to a custom log file
define('WP_DEBUG_LOG', WP_CONTENT_DIR . '/debug.log');
</pre>

<?php
// روش 2: اجرای این فایل برای تست
echo "<h1>تست Debug Mode</h1>";

if (file_exists('./wp-config.php')) {
    echo "<p>✅ فایل wp-config.php یافت شد</p>";
    
    $config = file_get_contents('./wp-config.php');
    
    if (strpos($config, "define('WP_DEBUG', true)") !== false) {
        echo "<p>✅ WP_DEBUG فعال است</p>";
    } else {
        echo "<p>❌ WP_DEBUG غیرفعال است</p>";
        echo "<p><strong>لطفاً این خطوط را به wp-config.php اضافه کنید:</strong></p>";
        echo "<pre>";
        echo "define('WP_DEBUG', true);\n";
        echo "define('WP_DEBUG_LOG', true);\n";
        echo "define('WP_DEBUG_DISPLAY', true);\n";
        echo "@ini_set('display_errors', 1);\n";
        echo "</pre>";
    }
    
    if (strpos($config, "define('WP_DEBUG_LOG', true)") !== false) {
        echo "<p>✅ WP_DEBUG_LOG فعال است</p>";
        
        $logFile = './wp-content/debug.log';
        if (file_exists($logFile)) {
            echo "<p>📁 فایل لاگ وجود دارد: <code>wp-content/debug.log</code></p>";
            echo "<p>آخرین 50 خط:</p>";
            echo "<pre style='background:#2d3748;color:#e2e8f0;padding:15px;overflow-x:auto;max-height:400px;'>";
            $lines = file($logFile);
            echo htmlspecialchars(implode('', array_slice($lines, -50)));
            echo "</pre>";
        } else {
            echo "<p>⚠️ فایل لاگ هنوز ایجاد نشده است</p>";
        }
    } else {
        echo "<p>❌ WP_DEBUG_LOG غیرفعال است</p>";
    }
} else {
    echo "<p>❌ فایل wp-config.php یافت نشد. این فایل باید در ریشه وردپرس قرار گیرد.</p>";
}

echo "<hr>";
echo "<h2>راهنمای فعال‌سازی Debug:</h2>";
echo "<ol>";
echo "<li>فایل <code>wp-config.php</code> را باز کنید</li>";
echo "<li>خطوط بالا را قبل از <code>/* That's all, stop editing! */</code> اضافه کنید</li>";
echo "<li>فایل را ذخیره کنید</li>";
echo "<li>به صفحه ایمپورت بروید و جستجو کنید</li>";
echo "<li>خطای دقیق در صفحه یا در فایل <code>wp-content/debug.log</code> نمایش داده می‌شود</li>";
echo "</ol>";
?>
