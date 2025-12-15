# حسابداری زرگر - Zargar Accounting WordPress Plugin

یک پلاگین WordPress حرفه‌ای برای یکپارچه‌سازی با سیستم حسابداری زرگر

## ویژگی‌ها

✅ **سیستم لاگ حرفه‌ای**
- سطوح مختلف لاگ (EMERGENCY, ALERT, CRITICAL, ERROR, WARNING, NOTICE, INFO, DEBUG)
- Log Rotation خودکار (هر فایل 5MB)
- نگهداری حداکثر 10 فایل لاگ
- حفاظت امنیتی از فایل‌های لاگ با `.htaccess`
- ثبت اطلاعات کاربر و زمان در هر لاگ

✅ **رابط کاربری ساده و زیبا**
- طراحی RTL فارسی
- 3 منوی اصلی (داشبورد، همگام‌سازی، گزارش‌ها)
- استفاده از Blade Templating Engine
- طراحی responsive و حرفه‌ای
- سازگار کامل با استایل WordPress Admin

✅ **سازگاری و امنیت**
- بدون تداخل با سایر پلاگین‌های WordPress
- استفاده از namespace مناسب (ZargarAccounting)
- رعایت کامل استانداردهای WordPress
- Autoloading با PSR-4
- مدیریت وابستگی‌ها با Composer

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

## استفاده از Logger

```php
$logger = \ZargarAccounting\Logger\Logger::getInstance();

$logger->info('پیام اطلاعاتی');
$logger->warning('پیام هشدار');
$logger->error('پیام خطا', ['context' => 'data']);
```

لاگ‌ها در مسیر `wp-content/uploads/zargar-accounting/logs/` ذخیره می‌شوند.

## استفاده از Blade Templates

```php
$blade = \ZargarAccounting\Core\BladeRenderer::getInstance();
echo $blade->render('admin.test1', [
    'title' => 'عنوان صفحه',
    'data' => $some_data
]);
```

## منوی ادمین

افزونه دو صفحه تست در منوی ادمین اضافه می‌کند:
- **Test 1**: صفحه آزمایشی اول
- **Test 2**: صفحه آزمایشی دوم

## توسعه

این افزونه با معماری تمیز و جدا از سایر افزونه‌ها طراحی شده است:

- همه کلاس‌ها در namespace `ZargarAccounting` قرار دارند
- استفاده از Singleton pattern برای کلاس‌های اصلی
- Autoloading با PSR-4
- کش جداگانه برای Blade templates
- سیستم لاگ مستقل با Rotating file handler

## نکات امنیتی

- همه فایل‌ها از دسترسی مستقیم محافظت شده‌اند
- استفاده از WordPress capabilities برای کنترل دسترسی
- لاگ‌ها خارج از دایرکتوری افزونه ذخیره می‌شوند

## مجوز

[MIT License](LICENSE)

## نویسنده

Seenday - https://seenday.com
