#!/bin/bash

# نمایش آخرین خطاهای PHP WordPress
echo "=== آخرین خطاهای PHP ==="
echo ""

# بررسی فایل debug.log
if [ -f "/var/www/html/wp-content/debug.log" ]; then
    tail -n 50 /var/www/html/wp-content/debug.log
elif [ -f "/home/morpheus/public_html/wp-content/debug.log" ]; then
    tail -n 50 /home/morpheus/public_html/wp-content/debug.log
else
    echo "❌ فایل debug.log یافت نشد"
    echo ""
    echo "برای فعال کردن debug در wp-config.php این خطوط را اضافه کنید:"
    echo ""
    echo "define('WP_DEBUG', true);"
    echo "define('WP_DEBUG_LOG', true);"
    echo "define('WP_DEBUG_DISPLAY', false);"
    echo "@ini_set('display_errors', 0);"
fi

echo ""
echo "=== لاگ‌های پلاگین زرگر ==="
echo ""

if [ -d "storage/logs" ]; then
    echo "📁 فایل‌های لاگ موجود:"
    find storage/logs -name "*.log" -type f -exec ls -lh {} \; | tail -20
    
    echo ""
    echo "📝 آخرین لاگ‌های general:"
    if [ -f "storage/logs/general/zargar-$(date +%Y-%m-%d).log" ]; then
        tail -n 20 "storage/logs/general/zargar-$(date +%Y-%m-%d).log"
    else
        echo "   فایل لاگ امروز یافت نشد"
    fi
else
    echo "❌ پوشه storage/logs یافت نشد"
fi
