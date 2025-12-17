# 📘 راهنمای توسعه‌دهنده - پلاگین حسابداری زرگر

> راهنمای کامل توسعه، معماری و نگهداری پروژه

---

## 📑 فهرست مطالب

1. [درباره پروژه](#درباره-پروژه)
2. [نصب و راه‌اندازی](#نصب-و-راه‌اندازی)
3. [معماری پروژه](#معماری-پروژه)
4. [ساختار پوشه‌ها](#ساختار-پوشه‌ها)
5. [کلاس‌های اصلی](#کلاس‌های-اصلی)
6. [توسعه ویژگی جدید](#توسعه-ویژگی-جدید)
7. [سیستم لاگ‌گذاری](#سیستم-لاگ‌گذاری)
8. [API و اتصال به سرور](#api-و-اتصال-به-سرور)
9. [Templating با Blade](#templating-با-blade)
10. [تست و دیباگ](#تست-و-دیباگ)
11. [استانداردها و قوانین](#استانداردها-و-قوانین)
12. [دیپلوی و انتشار](#دیپلوی-و-انتشار)

---

## 🎯 درباره پروژه

**نام پروژه:** Zargar Accounting WordPress Plugin  
**نسخه:** 1.0.0  
**زبان برنامه‌نویسی:** PHP 8.1+  
**فریمورک:** WordPress 5.8+  
**مخزن:** https://github.com/seenday-dev/zargar-accounting

### هدف
یکپارچه‌سازی فروشگاه WooCommerce با نرم‌افزار حسابداری زرگر برای همگام‌سازی خودکار محصولات، قیمت‌ها و سفارشات.

### ویژگی‌های کلیدی
- ✅ سیستم لاگ‌گذاری پیشرفته (4 نوع لاگ)
- ✅ اتصال امن به API حسابداری زرگر
- ✅ رابط کاربری مینیمال (Black & White)
- ✅ Blade Templating Engine
- ✅ PSR-4 Autoloading
- ✅ Monolog Logger
- ✅ AJAX-based UI

---

## ⚙️ نصب و راه‌اندازی

### پیش‌نیازها

```bash
PHP >= 8.1
WordPress >= 5.8
WooCommerce >= 6.0
Composer >= 2.0
```

### نصب Dependencies

```bash
cd wp-content/plugins/zargar-accounting
composer install --no-dev
```

### فعال‌سازی پلاگین

1. در پنل WordPress به **افزونه‌ها** بروید
2. پلاگین **Zargar Accounting** را فعال کنید
3. به **حسابداری زرگر → تنظیمات** بروید
4. اطلاعات سرور را وارد کنید

---

## 🏗️ معماری پروژه

### الگوی معماری: **MVC + Singleton**

```
┌─────────────┐
│   WordPress │
│    Plugin   │
└──────┬──────┘
       │
       ├─► Admin Layer (UI)
       │   ├─ MenuManager
       │   ├─ AssetsManager
       │   ├─ SettingsHandler
       │   └─ LogsViewer
       │
       ├─► Core Layer (Business Logic)
       │   ├─ BladeRenderer
       │   └─ Plugin Initialization
       │
       ├─► API Layer (External Communication)
       │   └─ ZargarApiClient
       │
       ├─► Logger Layer (Logging)
       │   ├─ MonologManager
       │   └─ LoggerAjax
       │
       └─► Database Layer (Data Access)
           ├─ Schema
           └─ ProductRepository
```

### Namespace Structure

```php
ZargarAccounting\
├── Admin\          # صفحات مدیریت
├── API\            # ارتباط با API
├── Core\           # منطق اصلی
├── Database\       # عملیات دیتابیس
└── Logger\         # سیستم لاگ
```

---

## 📁 ساختار پوشه‌ها

```
zargar-accounting/
│
├── assets/                      # فایل‌های استاتیک
│   ├── css/                     # استایل‌ها
│   │   ├── main.css            # استایل اصلی
│   │   ├── dashboard.css       # داشبورد
│   │   ├── forms.css           # فرم‌ها
│   │   ├── import.css          # صفحه ایمپورت
│   │   ├── logs.css            # صفحه لاگ‌ها
│   │   └── sidebar.css         # منوی سایدبار
│   ├── js/                      # اسکریپت‌های JavaScript
│   │   ├── main.js             # JS اصلی
│   │   ├── settings.js         # تست اتصال
│   │   └── logs.js             # مدیریت لاگ‌ها
│   └── icons/                   # آیکون‌ها
│       └── lineicons.css       # LineIcons
│
├── includes/                    # کلاس‌های PHP
│   ├── Admin/                   # مدیریت WordPress
│   │   ├── MenuManager.php     # منوی پلاگین
│   │   ├── AssetsManager.php   # بارگذاری CSS/JS
│   │   ├── SettingsHandler.php # تنظیمات و AJAX
│   │   ├── LogsViewer.php      # نمایش لاگ‌ها
│   │   ├── ImportHandler.php   # ایمپورت محصولات
│   │   ├── ProductMetaBox.php  # متاباکس محصول
│   │   └── DatabaseManager.php # مدیریت دیتابیس
│   │
│   ├── API/                     # ارتباط با سرور
│   │   └── ZargarApiClient.php # کلاینت API
│   │
│   ├── Core/                    # هسته پلاگین
│   │   └── BladeRenderer.php   # موتور Blade
│   │
│   ├── Database/                # لایه دیتابیس
│   │   ├── Schema.php          # ساختار جداول
│   │   └── ProductRepository.php # CRUD محصولات
│   │
│   └── Logger/                  # سیستم لاگ
│       ├── MonologManager.php  # مدیریت Monolog
│       ├── LoggerAjax.php      # AJAX handlers
│       ├── AdvancedLogger.php  # (Legacy)
│       └── Logger.php          # (Legacy)
│
├── templates/                   # قالب‌های Blade
│   ├── admin/                   # صفحات مدیریت
│   │   ├── dashboard.blade.php # داشبورد
│   │   ├── settings.blade.php  # تنظیمات
│   │   ├── import.blade.php    # ایمپورت
│   │   ├── logs.blade.php      # لاگ‌ها
│   │   └── database.blade.php  # دیتابیس
│   ├── components/              # کامپوننت‌های قابل استفاده مجدد
│   │   ├── header.blade.php    # هدر
│   │   ├── sidebar.blade.php   # منوی سمت راست
│   │   └── footer.blade.php    # فوتر
│   └── partials/                # بخش‌های کوچک
│       ├── breadcrumb.blade.php
│       ├── notifications.blade.php
│       └── pagination.blade.php
│
├── storage/                     # فایل‌های ذخیره‌سازی
│   ├── cache/                   # کش Blade
│   └── logs/                    # فایل‌های لاگ
│       ├── product/             # لاگ محصولات
│       ├── sales/               # لاگ فروش
│       ├── price/               # لاگ قیمت
│       └── error/               # لاگ خطاها
│
├── tests/                       # تست‌های PHPUnit
│   ├── BladeRendererTest.php
│   ├── LoggerAjaxTest.php
│   └── MonologManagerTest.php
│
├── vendor/                      # Composer dependencies
├── composer.json                # تنظیمات Composer
├── phpunit.xml                  # تنظیمات PHPUnit
├── README.md                    # مستندات کلی
└── zargar-accounting.php        # فایل اصلی پلاگین
```

---

## 🔧 کلاس‌های اصلی

### 1. MenuManager (مدیریت منو)

**مسیر:** `includes/Admin/MenuManager.php`

**نقش:** ایجاد منوی پلاگین در WordPress Admin

```php
namespace ZargarAccounting\Admin;

class MenuManager {
    private static $instance = null;
    
    public static function getInstance();
    public function registerHooks(): void;
    public function addMenuPages(): void;
}
```

**استفاده:**
```php
$menu_manager = MenuManager::getInstance();
$menu_manager->registerHooks();
```

---

### 2. AssetsManager (مدیریت Asset)

**مسیر:** `includes/Admin/AssetsManager.php`

**نقش:** بارگذاری CSS و JavaScript

```php
namespace ZargarAccounting\Admin;

class AssetsManager {
    public function enqueueStyles($hook): void;
    public function enqueueScripts($hook): void;
}
```

**CSS Enqueue:**
```php
wp_enqueue_style(
    'zargar-main',
    ZARGAR_ACCOUNTING_PLUGIN_URL . 'assets/css/main.css',
    [],
    ZARGAR_ACCOUNTING_VERSION
);
```

**JS Enqueue با Localize:**
```php
wp_enqueue_script('zargar-main', /* ... */);

wp_localize_script('zargar-main', 'zargarAjax', [
    'ajaxurl' => admin_url('admin-ajax.php'),
    'nonce' => wp_create_nonce('zargar_ajax_nonce')
]);
```

---

### 3. ZargarApiClient (ارتباط با API)

**مسیر:** `includes/API/ZargarApiClient.php`

**نقش:** ارتباط با سرور حسابداری زرگر

```php
namespace ZargarAccounting\API;

class ZargarApiClient {
    public function testConnection(): array;
    public function getUserKey(): ?string;
    public function reloadConfig(): void;
}
```

**استفاده:**
```php
$api = ZargarApiClient::getInstance();
$result = $api->testConnection();

if ($result['success']) {
    $userKey = $result['userkey'];
    // استفاده از UserKey
}
```

**پاسخ API:**
```php
[
    'success' => true,
    'message' => 'اتصال موفقیت‌آمیز',
    'userkey' => 'AAC90FD5-A43E-457A-...',
    'data' => [
        'server' => '37.235.18.235:8090',
        'username' => 'Service',
        'fullname' => 'Service Service'
    ]
]
```

---

### 4. MonologManager (لاگ‌گذاری)

**مسیر:** `includes/Logger/MonologManager.php`

**نقش:** مدیریت سیستم لاگ با Monolog

```php
namespace ZargarAccounting\Logger;

class MonologManager {
    // لاگ‌های عمومی
    public function info(string $message, array $context = []): void;
    public function success(string $message, array $context = []): void;
    public function warning(string $message, array $context = []): void;
    public function error(string $message, array $context = []): void;
    
    // لاگ‌های تخصصی
    public function product(string $message, array $context = []): void;
    public function sales(string $message, array $context = []): void;
    public function price(string $message, array $context = []): void;
}
```

**استفاده:**
```php
$logger = MonologManager::getInstance();

// لاگ محصول
$logger->product('محصول جدید ایجاد شد', [
    'product_id' => 123,
    'name' => 'گردنبند طلا'
]);

// لاگ خطا
$logger->error('خطا در اتصال به API', [
    'error' => $e->getMessage()
]);
```

**ساختار فایل لاگ:**
```
storage/logs/
├── product/
│   └── product-2025-12-17.log
├── sales/
│   └── sales-2025-12-17.log
├── price/
│   └── price-2025-12-17.log
└── error/
    └── error-2025-12-17.log
```

---

### 5. BladeRenderer (موتور Template)

**مسیر:** `includes/Core/BladeRenderer.php`

**نقش:** رندر کردن قالب‌های Blade

```php
namespace ZargarAccounting\Core;

class BladeRenderer {
    public function render(string $view, array $data = []): string;
}
```

**استفاده:**
```php
$blade = BladeRenderer::getInstance();

echo $blade->render('admin.dashboard', [
    'title' => 'داشبورد',
    'stats' => $statistics
]);
```

**قالب Blade:**
```blade
{{-- templates/admin/dashboard.blade.php --}}
@extends('components.layout')

@section('content')
    <h1>{{ $title }}</h1>
    <div class="stats">
        @foreach ($stats as $stat)
            <div class="stat-card">{{ $stat }}</div>
        @endforeach
    </div>
@endsection
```

---

### 6. SettingsHandler (تنظیمات)

**مسیر:** `includes/Admin/SettingsHandler.php`

**نقش:** مدیریت تنظیمات و تست اتصال

```php
namespace ZargarAccounting\Admin;

class SettingsHandler {
    public function saveSettings(): void;
    public function ajaxTestConnection(): void;
}
```

**AJAX Handler:**
```php
add_action('wp_ajax_zargar_test_connection', [
    $handler, 'ajaxTestConnection'
]);
```

**JavaScript:**
```javascript
$.ajax({
    url: zargarAjax.ajaxurl,
    type: 'POST',
    data: {
        action: 'zargar_test_connection',
        nonce: zargarAjax.testConnectionNonce,
        server_ip: '37.235.18.235',
        server_port: 8090
    },
    success: function(response) {
        if (response.success) {
            console.log(response.data.userkey);
        }
    }
});
```

---

## 🚀 توسعه ویژگی جدید

### مرحله 1: ایجاد کلاس جدید

**مثال:** اضافه کردن ویژگی گزارش‌گیری

```php
<?php
// includes/Admin/ReportsManager.php

namespace ZargarAccounting\Admin;

use ZargarAccounting\Logger\MonologManager;

class ReportsManager {
    private static $instance = null;
    private $logger;
    
    private function __construct() {
        $this->logger = MonologManager::getInstance();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function registerHooks(): void {
        add_action('admin_menu', [$this, 'addReportsPage']);
    }
    
    public function addReportsPage(): void {
        add_submenu_page(
            'zargar-accounting',
            'گزارش‌ها',
            'گزارش‌ها',
            'manage_options',
            'zargar-accounting-reports',
            [$this, 'renderReportsPage']
        );
    }
    
    public function renderReportsPage(): void {
        $blade = \ZargarAccounting\Core\BladeRenderer::getInstance();
        echo $blade->render('admin.reports', [
            'title' => 'گزارش‌های فروش'
        ]);
    }
}
```

### مرحله 2: ثبت در فایل اصلی

```php
// zargar-accounting.php

function zargar_accounting_init() {
    // ... سایر کدها
    
    // Initialize Reports Manager
    $reports_manager = ZargarAccounting\Admin\ReportsManager::getInstance();
    $reports_manager->registerHooks();
}
```

### مرحله 3: ایجاد Template

```blade
{{-- templates/admin/reports.blade.php --}}
@extends('components.layout')

@section('title', 'گزارش‌های فروش')

@section('content')
<div class="reports-container">
    <h1>{{ $title }}</h1>
    <div class="report-filters">
        {{-- فیلترها --}}
    </div>
    <div class="report-table">
        {{-- جدول گزارش --}}
    </div>
</div>
@endsection
```

### مرحله 4: اضافه کردن CSS/JS

```php
// includes/Admin/AssetsManager.php

if (strpos($hook, 'zargar-accounting-reports') !== false) {
    wp_enqueue_style(
        'zargar-reports',
        ZARGAR_ACCOUNTING_PLUGIN_URL . 'assets/css/reports.css',
        ['zargar-main'],
        ZARGAR_ACCOUNTING_VERSION
    );
}
```

---

## 📊 سیستم لاگ‌گذاری

### انواع لاگ

| نوع | متد | فایل | توضیحات |
|-----|------|------|---------|
| محصولات | `product()` | `storage/logs/product/` | ایجاد، ویرایش، حذف محصول |
| فروش | `sales()` | `storage/logs/sales/` | سفارشات و تراکنش‌ها |
| قیمت | `price()` | `storage/logs/price/` | تغییرات قیمت |
| عمومی | `info()` | `storage/logs/general/` | لاگ‌های عمومی |

### سطوح لاگ

```php
// INFO
$logger->info('عملیات موفق');

// SUCCESS
$logger->success('محصول با موفقیت ذخیره شد');

// WARNING
$logger->warning('قیمت به‌روزرسانی نشد');

// ERROR
$logger->error('خطا در اتصال به سرور', ['code' => 500]);
```

### Log Rotation

- **حداکثر اندازه:** 5 MB
- **حداکثر تعداد:** 30 فایل
- **نام‌گذاری:** `{type}-{date}.log`

### امنیت لاگ‌ها

فایل `.htaccess` در `storage/logs/`:

```apache
<Files "*.log">
    Order Allow,Deny
    Deny from all
</Files>
```

---

## 🔌 API و اتصال به سرور

### Endpoint اصلی

```
http://SERVER_IP:PORT/services/login/
```

### پارامترها

```
?username={username}&password={password}
```

### پاسخ موفق

```json
{
  "Status": "OK",
  "Result": {
    "UserId": "19",
    "UserKey": "AAC90FD5-A43E-457A-B8CC-65E265A9B477",
    "FirstName": "Service",
    "LastName": "Service",
    "DefaultPage": "/services/application/"
  },
  "Version": 1.2,
  "BusinessId": 20
}
```

### استفاده در کد

```php
$api = ZargarApiClient::getInstance();
$result = $api->testConnection();

if (!$result['success']) {
    $logger->error('خطا در اتصال', [
        'message' => $result['message']
    ]);
    return;
}

$userKey = $result['userkey'];
// استفاده از UserKey برای درخواست‌های بعدی
```

---

## 🎨 Templating با Blade

### ساختار Layout

```blade
{{-- templates/components/layout.blade.php --}}
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <title>@yield('title', 'حسابداری زرگر')</title>
</head>
<body>
    @include('components.sidebar')
    
    <main class="content">
        @yield('content')
    </main>
    
    @include('components.footer')
</body>
</html>
```

### استفاده از Layout

```blade
@extends('components.layout')

@section('title', 'داشبورد')

@section('content')
    <h1>سلام دنیا</h1>
@endsection
```

### Component ها

```blade
{{-- قطعه قابل استفاده مجدد --}}
@include('partials.notifications', [
    'type' => 'success',
    'message' => 'عملیات موفق بود'
])
```

### Directives

```blade
@if ($user->isAdmin())
    <button>مدیریت</button>
@else
    <p>دسترسی محدود</p>
@endif

@foreach ($products as $product)
    <div>{{ $product->name }}</div>
@endforeach

@empty($products)
    <p>محصولی یافت نشد</p>
@endempty
```

---

## 🧪 تست و دیباگ

### اجرای تست‌ها

```bash
# همه تست‌ها
composer test

# با Coverage
composer test:coverage

# تست ساده
composer test:simple
```

### نوشتن تست جدید

```php
<?php
// tests/ReportsManagerTest.php

use PHPUnit\Framework\TestCase;
use ZargarAccounting\Admin\ReportsManager;

class ReportsManagerTest extends TestCase {
    public function testInstanceCreation() {
        $manager = ReportsManager::getInstance();
        $this->assertInstanceOf(
            ReportsManager::class,
            $manager
        );
    }
    
    public function testSingletonPattern() {
        $instance1 = ReportsManager::getInstance();
        $instance2 = ReportsManager::getInstance();
        $this->assertSame($instance1, $instance2);
    }
}
```

### دیباگ با WP_DEBUG

```php
// wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
define('SCRIPT_DEBUG', true);
```

### بررسی لاگ‌ها

```bash
# لاگ WordPress
tail -f wp-content/debug.log

# لاگ پلاگین
tail -f wp-content/plugins/zargar-accounting/storage/logs/error/error-*.log
```

---

## 📏 استانداردها و قوانین

### Coding Standards

- **PSR-4:** Autoloading
- **PSR-12:** کدنویسی
- **WordPress Coding Standards**

### Naming Conventions

```php
// کلاس: PascalCase
class MenuManager {}

// متد: camelCase
public function registerHooks() {}

// متغیر: camelCase
$userName = 'Ali';

// ثابت: UPPER_SNAKE_CASE
define('ZARGAR_VERSION', '1.0.0');

// Action/Filter: lowercase با underscore
add_action('zargar_init', ...);
```

### File Structure

```php
<?php
/**
 * Class description
 * 
 * @package ZargarAccounting
 * @since 1.0.0
 */

namespace ZargarAccounting\Admin;

// Use statements
use ZargarAccounting\Logger\MonologManager;

// Class definition
class ClassName {
    // Properties
    private static $instance = null;
    
    // Constructor
    private function __construct() {}
    
    // Static methods
    public static function getInstance() {}
    
    // Public methods
    public function publicMethod() {}
    
    // Private methods
    private function privateMethod() {}
}
```

### Security Best Practices

```php
// 1. Escape output
echo esc_html($user_input);
echo esc_url($url);
echo esc_attr($attribute);

// 2. Sanitize input
$clean = sanitize_text_field($_POST['name']);
$email = sanitize_email($_POST['email']);

// 3. Verify nonces
if (!wp_verify_nonce($_POST['_wpnonce'], 'action_name')) {
    wp_die('Security check failed');
}

// 4. Check capabilities
if (!current_user_can('manage_options')) {
    wp_die('Access denied');
}

// 5. Prepare SQL
$wpdb->prepare("SELECT * FROM table WHERE id = %d", $id);
```

---

## 📦 دیپلوی و انتشار

### مرحله 1: آماده‌سازی

```bash
# نصب dependencies بدون dev
composer install --no-dev --optimize-autoloader

# حذف فایل‌های غیرضروری
rm -rf tests/
rm -rf .git/
rm -f .gitignore composer.lock
```

### مرحله 2: ایجاد Package

```bash
# ایجاد ZIP
cd wp-content/plugins/
zip -r zargar-accounting.zip zargar-accounting/ \
    -x "*/node_modules/*" \
    -x "*/tests/*" \
    -x "*/.git/*"
```

### مرحله 3: آپلود به سرور

```bash
# با FTP یا cPanel
# یا با WP-CLI:
wp plugin install zargar-accounting.zip
wp plugin activate zargar-accounting
```

### مرحله 4: بررسی سلامت

در WordPress Admin:
1. فعال‌سازی پلاگین
2. بررسی منو
3. تست اتصال به API
4. بررسی لاگ‌ها

### Checklist قبل از Release

- [ ] همه تست‌ها پاس شده
- [ ] Composer dependencies نصب شده
- [ ] فایل‌های تست حذف شده
- [ ] نسخه در `zargar-accounting.php` به‌روز شده
- [ ] `CHANGELOG.md` نوشته شده
- [ ] مستندات کامل است
- [ ] بررسی امنیتی انجام شده

---

## 🆘 عیب‌یابی رایج

### مشکل: پلاگین فعال نمی‌شود

```bash
# بررسی نسخه PHP
php -v  # باید >= 8.1 باشد

# بررسی Composer
composer install
```

### مشکل: خطای "Class not found"

```bash
# Autoload را بازسازی کنید
composer dump-autoload
```

### مشکل: لاگ‌ها ذخیره نمی‌شوند

```bash
# بررسی دسترسی
chmod -R 755 storage/
chmod -R 777 storage/logs/
```

### مشکل: AJAX کار نمی‌کند

```javascript
// بررسی Console مرورگر
console.log(zargarAjax);  // باید تعریف شده باشد

// بررسی نام action
// PHP: add_action('wp_ajax_ACTION_NAME', ...)
// JS: data: { action: 'ACTION_NAME' }
```

---

## 📚 منابع مفید

### مستندات رسمی
- [WordPress Plugin Handbook](https://developer.wordpress.org/plugins/)
- [WooCommerce Docs](https://woocommerce.com/documentation/)
- [Monolog Documentation](https://github.com/Seldaek/monolog)
- [Blade Templates](https://laravel.com/docs/blade)

### ابزارها
- [WP-CLI](https://wp-cli.org/) - مدیریت WordPress از خط فرمان
- [Query Monitor](https://wordpress.org/plugins/query-monitor/) - دیباگ WordPress
- [PHPUnit](https://phpunit.de/) - تست PHP

---

## 👥 مشارکت در پروژه

### گزارش باگ

Issues را در GitHub ثبت کنید:
```
https://github.com/seenday-dev/zargar-accounting/issues
```

### Pull Request

1. Fork کنید
2. برنچ جدید بسازید: `git checkout -b feature/my-feature`
3. Commit کنید: `git commit -m "Add feature"`
4. Push کنید: `git push origin feature/my-feature`
5. PR بسازید

### کد استایل

قبل از commit کد را فرمت کنید:
```bash
vendor/bin/phpcs --standard=PSR12 includes/
vendor/bin/phpcbf --standard=PSR12 includes/
```

---

## 📝 تاریخچه نسخه‌ها

### v1.0.0 (2025-12-17)
- ✨ نسخه اولیه
- ✅ سیستم لاگ‌گذاری 4 نوعی
- ✅ اتصال به API زرگر
- ✅ رابط کاربری مینیمال
- ✅ Blade Templating

---

## 📞 پشتیبانی

**ایمیل:** support@seenday.com  
**وب‌سایت:** https://seenday.com  
**GitHub:** https://github.com/seenday-dev/zargar-accounting

---

**✨ موفق باشید!**

تهیه شده با ❤️ توسط تیم Seenday
