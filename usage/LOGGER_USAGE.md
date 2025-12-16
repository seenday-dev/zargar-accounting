# راهنمای استفاده از سیستم لاگ با Monolog

## معرفی

این پلاگین از کتابخانه قدرتمند **Monolog** برای مدیریت لاگ‌ها استفاده می‌کند. Monolog یک کتابخانه استاندارد و حرفه‌ای برای logging در PHP است که قابلیت‌های زیادی دارد.

## دسترسی به Logger

برای شروع، ابتدا باید به instance اصلی `MonologManager` دسترسی داشته باشید:

```php
use ZargarAccounting\Logger\MonologManager;

// دریافت instance
$logger = MonologManager::getInstance();
```

## کانال‌های (Channels) موجود

سیستم دارای 5 کانال مجزا است:

1. **product** - لاگ‌های مربوط به محصولات
2. **sales** - لاگ‌های مربوط به فروش
3. **price** - لاگ‌های مربوط به قیمت‌ها
4. **error** - لاگ‌های خطاها
5. **general** - لاگ‌های عمومی

## مثال‌های کاربردی

### 1. لاگ کردن رویدادهای محصول

```php
$logger = MonologManager::getInstance();

// لاگ اطلاعات عادی
$logger->product('محصول جدید اضافه شد', [
    'product_id' => 123,
    'product_name' => 'گوشی موبایل',
    'sku' => 'MOB-001'
]);

// لاگ خطا
$logger->productError('خطا در به‌روزرسانی محصول', [
    'product_id' => 123,
    'error' => 'اتصال به API قطع شد'
]);

// لاگ هشدار
$logger->productWarning('موجودی کم است', [
    'product_id' => 123,
    'stock' => 2
]);
```

### 2. لاگ کردن فروش

```php
// لاگ یک فروش موفق
$logger->sales('سفارش جدید ثبت شد', [
    'order_id' => 456,
    'customer' => 'علی احمدی',
    'total' => 1500000
]);

// لاگ خطای فروش
$logger->salesError('خطا در ثبت سفارش', [
    'order_id' => 456,
    'error_message' => 'اطلاعات مشتری ناقص است'
]);
```

### 3. لاگ کردن تغییرات قیمت

```php
// ثبت تغییر قیمت
$logger->price('قیمت محصول به‌روزرسانی شد', [
    'product_id' => 789,
    'old_price' => 500000,
    'new_price' => 550000,
    'change_reason' => 'افزایش نرخ ارز'
]);

// لاگ خطا در بروزرسانی قیمت
$logger->priceError('خطا در به‌روزرسانی قیمت', [
    'product_id' => 789,
    'error' => 'قیمت نامعتبر است'
]);
```

### 4. لاگ کردن خطاها

```php
// لاگ خطای عادی
$logger->error('خطای اتصال به API', [
    'api_url' => 'https://api.example.com',
    'http_code' => 500
]);

// لاگ خطای بحرانی
$logger->critical('پایگاه داده در دسترس نیست', [
    'database' => 'zargar_db',
    'host' => 'localhost'
]);
```

### 5. لاگ‌های عمومی

```php
// لاگ اطلاعات عمومی
$logger->info('کاربر وارد شد', [
    'user_id' => 1,
    'username' => 'admin'
]);

// لاگ دیباگ (برای توسعه)
$logger->debug('متغیر تست', [
    'variable' => $some_var,
    'type' => gettype($some_var)
]);

// لاگ هشدار عمومی
$logger->warning('حافظه در حال پر شدن است', [
    'memory_usage' => '85%'
]);
```

## استفاده در کلاس‌ها و ماژول‌ها

### مثال: کلاس Product Sync

```php
<?php

namespace YourNamespace;

use ZargarAccounting\Logger\MonologManager;

class ProductSync {
    private $logger;
    
    public function __construct() {
        $this->logger = MonologManager::getInstance();
    }
    
    public function syncProducts() {
        $this->logger->product('شروع همگام‌سازی محصولات');
        
        try {
            // دریافت محصولات از API
            $products = $this->getProductsFromAPI();
            
            $this->logger->product('دریافت محصولات موفق', [
                'count' => count($products)
            ]);
            
            foreach ($products as $product) {
                try {
                    $this->syncSingleProduct($product);
                    
                    $this->logger->product('محصول همگام شد', [
                        'product_id' => $product['id'],
                        'name' => $product['name']
                    ]);
                } catch (\Exception $e) {
                    $this->logger->productError('خطا در همگام‌سازی محصول', [
                        'product_id' => $product['id'],
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            $this->logger->product('همگام‌سازی کامل شد');
            
        } catch (\Exception $e) {
            $this->logger->critical('خطای بحرانی در همگام‌سازی', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    
    private function getProductsFromAPI() {
        // کد دریافت محصولات...
    }
    
    private function syncSingleProduct($product) {
        // کد همگام‌سازی یک محصول...
    }
}
```

### مثال: کلاس Order Handler

```php
<?php

namespace YourNamespace;

use ZargarAccounting\Logger\MonologManager;

class OrderHandler {
    private $logger;
    
    public function __construct() {
        $this->logger = MonologManager::getInstance();
    }
    
    public function processOrder($order_id) {
        $this->logger->sales('شروع پردازش سفارش', [
            'order_id' => $order_id
        ]);
        
        // بررسی اعتبار سفارش
        if (!$this->validateOrder($order_id)) {
            $this->logger->salesError('سفارش نامعتبر است', [
                'order_id' => $order_id
            ]);
            return false;
        }
        
        // پردازش پرداخت
        $payment_result = $this->processPayment($order_id);
        
        if ($payment_result) {
            $this->logger->sales('پرداخت موفق', [
                'order_id' => $order_id,
                'amount' => $payment_result['amount']
            ]);
        } else {
            $this->logger->salesError('پرداخت ناموفق', [
                'order_id' => $order_id
            ]);
            return false;
        }
        
        return true;
    }
}
```

## دسترسی مستقیم به Logger خاص

اگر می‌خواهید مستقیماً به یک logger خاص دسترسی داشته باشید:

```php
$manager = MonologManager::getInstance();

// دریافت logger محصولات
$product_logger = $manager->getLogger(MonologManager::CHANNEL_PRODUCT);

// استفاده مستقیم
$product_logger->info('پیام تست', ['data' => 'value']);
$product_logger->error('خطای تست');
```

## مدیریت لاگ‌ها

### خواندن لاگ‌های اخیر

```php
$manager = MonologManager::getInstance();

// دریافت 50 لاگ آخر از کانال product
$logs = $manager->getRecentLogs(MonologManager::CHANNEL_PRODUCT, 50);

foreach ($logs as $log) {
    echo $log['time'] . ' - ' . $log['level'] . ': ' . $log['message'];
}
```

### دریافت آمار لاگ‌ها

```php
$manager = MonologManager::getInstance();

$stats = $manager->getStats(MonologManager::CHANNEL_PRODUCT);

echo 'تعداد کل لاگ‌ها: ' . $stats['total'];
echo 'حجم فایل: ' . $stats['size'] . ' بایت';
echo 'آخرین بروزرسانی: ' . $stats['last_modified'];
```

### پاک کردن لاگ‌ها

```php
$manager = MonologManager::getInstance();

// پاک کردن تمام لاگ‌های محصولات
$deleted = $manager->clearLogs(MonologManager::CHANNEL_PRODUCT);

echo "تعداد {$deleted} فایل لاگ حذف شد";
```

## سطوح لاگ (Log Levels)

Monolog از سطوح استاندارد PSR-3 استفاده می‌کند:

1. **DEBUG** - اطلاعات دیباگ (برای توسعه)
2. **INFO** - اطلاعات عمومی
3. **NOTICE** - رویدادهای عادی اما قابل توجه
4. **WARNING** - هشدارها
5. **ERROR** - خطاهای زمان اجرا
6. **CRITICAL** - شرایط بحرانی
7. **ALERT** - نیاز به اقدام فوری
8. **EMERGENCY** - سیستم غیرقابل استفاده

## نکات مهم

### ✅ بهترین روش‌ها (Best Practices)

1. **استفاده از Context**: همیشه اطلاعات مفید را در context قرار دهید
```php
// ❌ بد
$logger->error('خطا رخ داد');

// ✅ خوب
$logger->error('خطا در ارسال ایمیل', [
    'user_id' => 123,
    'email' => 'user@example.com',
    'error_code' => 'SMTP_CONNECTION_FAILED'
]);
```

2. **پیام‌های واضح**: پیام‌های لاگ باید خوانا و مفید باشند
```php
// ❌ بد
$logger->info('OK');

// ✅ خوب
$logger->info('محصول با موفقیت به پایگاه داده اضافه شد');
```

3. **استفاده از کانال مناسب**: لاگ‌ها را در کانال صحیح ثبت کنید
```php
// مربوط به محصولات
$logger->product('...');

// مربوط به فروش
$logger->sales('...');

// خطاهای سیستم
$logger->error('...');
```

## مکان ذخیره‌سازی لاگ‌ها

لاگ‌ها در مسیر زیر ذخیره می‌شوند:

```
wp-content/plugins/zargar-accounting/storage/logs/
├── product/
│   ├── product.log
│   ├── product-2025-12-14.log
│   └── product-2025-12-13.log
├── sales/
│   └── sales.log
├── price/
│   └── price.log
├── error/
│   └── error.log
└── general/
    └── general.log
```

هر کانال فایل‌های خود را به صورت روزانه rotate می‌کند و تا 30 روز نگهداری می‌شود.

## امنیت

- تمام فایل‌های لاگ توسط `.htaccess` محافظت می‌شوند
- دسترسی مستقیم از طریق مرورگر غیرممکن است
- فقط کاربران با دسترسی `manage_options` می‌توانند لاگ‌ها را مشاهده کنند

## پشتیبانی و سوالات

برای سوالات بیشتر، به مستندات Monolog مراجعه کنید:
https://github.com/Seldaek/monolog

---

**نسخه:** 2.0.0  
**تاریخ:** 2025-12-15
