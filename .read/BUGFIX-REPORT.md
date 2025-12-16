# 🐛 Bug Fix Report - نسخه 2.0.1

## ✅ مشکلات برطرف شده

### 1. "در حال بارگذاری..." دائمی
- رفع تایپو: `parsLogLine` → `parseLogLine`
- پشتیبانی از فایل‌های rotated در `getRecentLogs()`
- خواندن از تمام فایل‌های `*.log`

### 2. آمار صفر در تب‌ها (📦💰💵⚠️)
- اصلاح `getStats()` برای جمع تمام فایل‌ها
- نمایش صحیح تعداد لاگ‌ها

### 3. فونت فارسی
- تغییر از Iranian Sans به Yekan
- CDN: jsdelivr.net

### 4. عدم بارگذاری CSS
- اضافه کردن enqueue برای main.css, sidebar.css, logs.css

### 5. Debug Logging
- اضافه کردن console.log در logs.js

## 📁 فایل‌های تغییر یافته

1. `includes/Logger/MonologManager.php` ✓
2. `includes/Admin/AssetsManager.php` ✓
3. `assets/css/main.css` ✓
4. `assets/js/logs.js` ✓

## 🧪 تست

```bash
./vendor/bin/phpunit --testdox
✅ 22 tests, 32 assertions - OK
```

---
**نسخه:** 2.0.1 | **تاریخ:** 2025-12-15 | **وضعیت:** ✅ Fixed
