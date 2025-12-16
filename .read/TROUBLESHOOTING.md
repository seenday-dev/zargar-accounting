# 🔧 راهنمای سریع عیب‌یابی

## مشکل: فونت یکان نمایش داده نمی‌شود
✅ راه‌حل: Cache مرورگر را پاک کنید (Ctrl+Shift+Delete)

## مشکل: "در حال بارگذاری..." دائمی  
✅ راه‌حل:
```bash
php generate-test-logs.php
chmod -R 777 storage/logs/
```

## مشکل: آمار صفر در تب‌ها
✅ راه‌حل:
```bash
php generate-test-logs.php
```

## تست سریع:
```bash
cd /home/morpheus/Documents/zargar-accounting
composer dump-autoload
php generate-test-logs.php
./vendor/bin/phpunit --testdox
```

✅ اگر بدون خطا بود، همه چیز درسته!
