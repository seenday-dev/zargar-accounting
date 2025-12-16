# حسابداری زرگر - Zargar Accounting WordPress Plugin

یک پلاگین WordPress حرفه‌ای برای یکپارچه‌سازی با سیستم حسابداری زرگر

## ویژگی‌ها

✅ **سیستم لاگ پیشرفته با 4 نوع مجزا**
- **لاگ محصولات**: ثبت همگام‌سازی و تغییرات محصولات
- **لاگ فروش**: ثبت تراکنش‌ها و سفارشات
- **لاگ قیمت**: ثبت تغییرات قیمت‌گذاری
- **لاگ خطا**: ثبت خطاها و مشکلات سیستم
- 4 سطح لاگ (INFO, SUCCESS, WARNING, ERROR)
- Log Rotation خودکار (هر فایل 5MB، نگهداری 30 فایل)
- فایل‌های مجزا برای هر نوع و هر روز
- حفاظت امنیتی با `.htaccess`
- AJAX switching بدون رفرش صفحه
- فیلتر بر اساس سطح لاگ
- نمایش Context و جزئیات

✅ **رابط کاربری مینیمال و حرفه‌ای**
- طراحی Black & White مینیمال
- فونت Iranian Sans
- اندازه‌های 50% کوچکتر برای فضای بیشتر
- تب‌های تعاملی با AJAX
- جداول responsive
- Loading states و Empty states
- طراحی سازگار با Figma designs

✅ **Blade Templating Engine**
- استفاده از Laravel Blade برای templates
- Component-based architecture
- Sidebar، Header، Footer جداگانه
- Custom directives (@nonce, @can)

✅ **سازگاری و امنیت**
- WordPress 5.8+ و PHP 8.1+
- PSR-4 Autoloading
- Namespace مناسب (ZargarAccounting)
- بدون تداخل با پلاگین‌های دیگر
- Nonce verification برای AJAX
- Capability checks

## نصب و راه‌اندازی

### پیش‌نیازها

- PHP >= 8.1
- WordPress >= 5.8
- Composer

### مراحل نصب

1. کلون کردن پروژه در پوشه افزونه‌های WordPress:
```bash
cd wp-content/plugins
git clone [repository-url] zargar-accounting
cd zargar-accounting
```

2. نصب وابستگی‌ها:
```bash
composer install
```

3. فعال‌سازی افزونه از پنل مدیریت WordPress

## ساختار پروژه

```
zargar-accounting/
├── zargar-accounting.php      # فایل اصلی افزونه
├── composer.json               # وابستگی‌های Composer
├── includes/                   # کلاس‌های PHP
│   ├── core/                   # کلاس‌های اصلی
│   │   └── BladeRenderer.php   # رندر کننده Blade
│   └── logger/                 # سیستم لاگ
│       └── Logger.php          # کلاس Logger با Monolog
├── templates/                  # تمپلیت‌ها
│   └── views/                  # تمپلیت‌های Blade
│       ├── layouts/            # Layout اصلی
│       │   └── app.blade.php   # Layout پایه
│       ├── components/         # کامپوننت‌ها
│       │   └── sidebar.blade.php
│       └── admin/              # صفحات ادمین
│           ├── test1.blade.php
│           └── test2.blade.php
├── storage/                    # فایل‌های ذخیره‌سازی
│   ├── cache/                  # کش Blade
│   └── logs/                   # فایل‌های لاگ
└── assets/                     # فایل‌های استاتیک
    ├── css/
    ├── js/
    └── images/
```

## استفاده از سیستم لاگ پیشرفته

```php
use ZargarAccounting\Logger\AdvancedLogger;

$logger = AdvancedLogger::getInstance();

// لاگ محصولات
$logger->logProduct('محصول جدید اضافه شد', AdvancedLogger::LEVEL_SUCCESS, [
    'product_id' => 123,
    'name' => 'گردنبند طلا'
]);

// لاگ فروش
$logger->logSales('سفارش ثبت شد', AdvancedLogger::LEVEL_INFO, [
    'order_id' => 456,
    'amount' => 5000000
]);

// لاگ قیمت
$logger->logPrice('قیمت طلا تغییر کرد', AdvancedLogger::LEVEL_WARNING, [
    'old_price' => 3500000,
    'new_price' => 3800000
]);

// لاگ خطا
$logger->logError('خطا در اتصال به سرور', [
    'server' => '192.168.1.100',
    'error' => 'Connection timeout'
]);

// دریافت لاگ‌ها
$logs = $logger->getLogs(AdvancedLogger::TYPE_PRODUCT, 100);
$stats = $logger->getLogStats(AdvancedLogger::TYPE_SALES);
```

برای راهنمای کامل، `LOGGING_GUIDE.md` را مطالعه کنید.

لاگ‌ها در مسیر `storage/logs/` با ساختار زیر ذخیره می‌شوند:
```
storage/logs/
├── product/
│   └── zargar-product-2024-01-15.log
├── sales/
│   └── zargar-sales-2024-01-15.log
├── price/
│   └── zargar-price-2024-01-15.log
└── error/
    └── zargar-error-2024-01-15.log
```

## استفاده از Blade Templates

```php
$blade = \ZargarAccounting\Core\BladeRenderer::getInstance();
echo $blade->render('admin.test1', [
    'title' => 'عنوان صفحه',
    'data' => $some_data
]);
```

## منوی ادمین

پلاگین شامل 3 صفحه اصلی در پنل مدیریت است:
- **داشبورد**: نمای کلی و ویجت‌های آمار
- **تنظیمات**: پیکربندی اتصال به سرور حسابداری
- **گزارش‌ها**: مشاهده و مدیریت لاگ‌ها (4 تب: محصولات، فروش، قیمت، خطاها)

## تست سیستم لاگ

برای تست سیستم لاگ:

1. فایل `test-advanced-logger.php` را در root وردپرس قرار دهید
2. به آدرس `yoursite.com/test-advanced-logger.php` بروید
3. لاگ‌های نمونه ایجاد می‌شوند
4. سپس به صفحه گزارش‌ها در پنل مدیریت بروید
5. بعد از تست، فایل را حذف کنید

## توسعه

این پلاگین با معماری تمیز و حرفه‌ای طراحی شده است:

- **Namespace**: تمام کلاس‌ها در `ZargarAccounting` namespace
- **Design Pattern**: Singleton برای کلاس‌های اصلی
- **Autoloading**: PSR-4 با Composer
- **Templating**: Laravel Blade Engine
- **CSS Architecture**: CSS Variables برای تم مینیمال
- **AJAX**: بدون رفرش صفحه برای تعامل سریع
- **Security**: Nonce verification، Capability checks، .htaccess protection
- **Performance**: Log rotation، Caching

## فایل‌های مهم

- `zargar-accounting.php` - فایل اصلی پلاگین
- `includes/Logger/AdvancedLogger.php` - سیستم لاگ پیشرفته
- `includes/Logger/LoggerAjax.php` - AJAX handlers
- `includes/Core/BladeRenderer.php` - Blade renderer
- `assets/js/logs.js` - JavaScript برای لاگ‌ها
- `assets/css/` - استایل‌های مینیمال
- `templates/` - Blade templates
- `LOGGING_GUIDE.md` - راهنمای کامل لاگ
- `test-advanced-logger.php` - فایل تست

## نکات امنیتی

- همه فایل‌ها از دسترسی مستقیم محافظت شده‌اند
- استفاده از WordPress capabilities برای کنترل دسترسی
- لاگ‌ها خارج از دایرکتوری افزونه ذخیره می‌شوند

## مجوز

[MIT License](LICENSE)

## نویسنده

Seenday - https://seenday.com

## 🧪 تست‌ها

این پروژه دارای سیستم تست کامل با PHPUnit است:

```bash
# اجرای تمام تست‌ها
composer test

# یا
./vendor/bin/phpunit --testdox

# اجرای تست ساده
php tests/run-tests.php
```

**نتیجه تست‌ها:**
- ✅ 22 تست
- ✅ 32 assertion
- ✅ 100% موفق

برای اطلاعات بیشتر: [راهنمای تست‌ها](tests/README.md)

