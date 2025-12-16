# سیستم لاگ پیشرفته - راهنمای استفاده

## معرفی

سیستم لاگ پیشرفته Zargar Accounting شامل 4 نوع لاگ مجزا است:

1. **لاگ محصولات** (Product Logs) - ثبت همگام‌سازی و تغییرات محصولات
2. **لاگ فروش** (Sales Logs) - ثبت تراکنش‌ها و فروش
3. **لاگ قیمت** (Price Logs) - ثبت تغییرات قیمت
4. **لاگ خطا** (Error Logs) - ثبت خطاها و مشکلات

## نحوه استفاده

### 1. دریافت Instance لاگر

```php
use ZargarAccounting\Logger\AdvancedLogger;

$logger = AdvancedLogger::getInstance();
```

### 2. ثبت لاگ محصولات

```php
// مثال 1: لاگ ساده
$logger->logProduct('محصول جدید اضافه شد');

// مثال 2: لاگ با سطح SUCCESS
$logger->logProduct('همگام‌سازی محصولات با موفقیت انجام شد', AdvancedLogger::LEVEL_SUCCESS);

// مثال 3: لاگ با context اطلاعات بیشتر
$logger->logProduct(
    'محصول آپدیت شد',
    AdvancedLogger::LEVEL_INFO,
    [
        'product_id' => 123,
        'product_name' => 'گردنبند طلا',
        'changes' => ['stock' => 10]
    ]
);

// مثال 4: لاگ هشدار
$logger->logProduct(
    'موجودی محصول کم است',
    AdvancedLogger::LEVEL_WARNING,
    [
        'product_id' => 456,
        'current_stock' => 2,
        'threshold' => 5
    ]
);
```

### 3. ثبت لاگ فروش

```php
// مثال 1: لاگ فروش موفق
$logger->logSales(
    'سفارش جدید ثبت شد',
    AdvancedLogger::LEVEL_SUCCESS,
    [
        'order_id' => 789,
        'customer' => 'علی احمدی',
        'total' => 5000000,
        'items_count' => 3
    ]
);

// مثال 2: لاگ خطای پرداخت
$logger->logSales(
    'پرداخت ناموفق بود',
    AdvancedLogger::LEVEL_ERROR,
    [
        'order_id' => 790,
        'error_code' => 'PAYMENT_FAILED',
        'gateway' => 'mellat'
    ]
);

// مثال 3: لاگ کنسلی سفارش
$logger->logSales(
    'سفارش کنسل شد',
    AdvancedLogger::LEVEL_WARNING,
    [
        'order_id' => 791,
        'reason' => 'درخواست مشتری'
    ]
);
```

### 4. ثبت لاگ قیمت

```php
// مثال 1: تغییر قیمت محصول
$logger->logPrice(
    'قیمت محصول به‌روز شد',
    AdvancedLogger::LEVEL_INFO,
    [
        'product_id' => 123,
        'old_price' => 4500000,
        'new_price' => 4800000,
        'change_percent' => 6.67
    ]
);

// مثال 2: قیمت‌گذاری انبوه
$logger->logPrice(
    'قیمت‌گذاری انبوه انجام شد',
    AdvancedLogger::LEVEL_SUCCESS,
    [
        'products_count' => 50,
        'increase_percent' => 10
    ]
);

// مثال 3: هشدار تغییر قیمت زیاد
$logger->logPrice(
    'افزایش قیمت بیش از حد مجاز',
    AdvancedLogger::LEVEL_WARNING,
    [
        'product_id' => 999,
        'change_percent' => 35,
        'max_allowed' => 20
    ]
);
```

### 5. ثبت لاگ خطا

```php
// مثال 1: خطای اتصال
$logger->logError(
    'خطا در اتصال به سرور حسابداری',
    [
        'server' => '192.168.1.100',
        'port' => 8080,
        'error' => 'Connection timeout'
    ]
);

// مثال 2: خطای دیتابیس
$logger->logError(
    'خطا در ذخیره‌سازی داده',
    [
        'table' => 'products',
        'sql_error' => 'Duplicate entry',
        'attempted_id' => 123
    ]
);

// مثال 3: خطای API
$logger->logError(
    'پاسخ نامعتبر از API',
    [
        'endpoint' => '/api/products',
        'status_code' => 500,
        'response' => 'Internal Server Error'
    ]
);
```

## سطوح لاگ (Log Levels)

```php
AdvancedLogger::LEVEL_INFO      // اطلاع‌رسانی عادی (آبی)
AdvancedLogger::LEVEL_SUCCESS   // عملیات موفق (سبز)
AdvancedLogger::LEVEL_WARNING   // هشدار (زرد)
AdvancedLogger::LEVEL_ERROR     // خطا (قرمز)
```

## دریافت لاگ‌ها

```php
// دریافت لاگ‌های محصولات
$logs = $logger->getLogs(AdvancedLogger::TYPE_PRODUCT, 100);

// دریافت فقط خطاها
$errors = $logger->getLogs(
    AdvancedLogger::TYPE_PRODUCT,
    50,
    AdvancedLogger::LEVEL_ERROR
);

// دریافت آمار
$stats = $logger->getLogStats(AdvancedLogger::TYPE_PRODUCT);
// نتیجه: ['total' => 150, 'info' => 80, 'success' => 50, 'warning' => 15, 'error' => 5]

// حذف لاگ‌ها
$deleted = $logger->clearLogs(AdvancedLogger::TYPE_PRODUCT);
```

## مثال کامل: همگام‌سازی محصولات

```php
use ZargarAccounting\Logger\AdvancedLogger;

function sync_products_from_accounting() {
    $logger = AdvancedLogger::getInstance();
    
    // شروع همگام‌سازی
    $logger->logProduct('شروع همگام‌سازی محصولات', AdvancedLogger::LEVEL_INFO, [
        'source' => 'accounting_api',
        'user_id' => get_current_user_id()
    ]);
    
    try {
        // اتصال به API
        $products = fetch_products_from_api();
        
        if (empty($products)) {
            $logger->logProduct(
                'هیچ محصولی برای همگام‌سازی یافت نشد',
                AdvancedLogger::LEVEL_WARNING
            );
            return;
        }
        
        $success_count = 0;
        $error_count = 0;
        
        foreach ($products as $product) {
            try {
                // ایجاد یا آپدیت محصول
                $result = update_woocommerce_product($product);
                
                if ($result) {
                    $success_count++;
                    
                    // لاگ موفقیت
                    $logger->logProduct(
                        'محصول همگام شد',
                        AdvancedLogger::LEVEL_SUCCESS,
                        [
                            'product_id' => $product['id'],
                            'name' => $product['name'],
                            'price' => $product['price']
                        ]
                    );
                    
                    // اگر قیمت تغییر کرده، لاگ قیمت بزن
                    if (isset($product['price_changed'])) {
                        $logger->logPrice(
                            'قیمت محصول تغییر کرد',
                            AdvancedLogger::LEVEL_INFO,
                            [
                                'product_id' => $product['id'],
                                'old_price' => $product['old_price'],
                                'new_price' => $product['price']
                            ]
                        );
                    }
                } else {
                    $error_count++;
                    throw new Exception('خطا در ذخیره محصول');
                }
                
            } catch (Exception $e) {
                $error_count++;
                
                $logger->logError(
                    'خطا در همگام‌سازی محصول',
                    [
                        'product_id' => $product['id'],
                        'error' => $e->getMessage()
                    ]
                );
            }
        }
        
        // لاگ نهایی
        $logger->logProduct(
            'همگام‌سازی محصولات تکمیل شد',
            $error_count > 0 ? AdvancedLogger::LEVEL_WARNING : AdvancedLogger::LEVEL_SUCCESS,
            [
                'total' => count($products),
                'success' => $success_count,
                'errors' => $error_count
            ]
        );
        
    } catch (Exception $e) {
        $logger->logError(
            'خطای کلی در همگام‌سازی محصولات',
            [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]
        );
    }
}
```

## ویژگی‌های سیستم

### 1. Rotation خودکار
- فایل‌های لاگ بالای 5MB به صورت خودکار Rotate می‌شوند
- حداکثر 30 فایل لاگ نگهداری می‌شود
- فایل‌های قدیمی حذف می‌شوند

### 2. فایل‌های مجزا
- هر نوع لاگ در پوشه جداگانه
- فایل جدید برای هر روز
- مثال: `storage/logs/product/zargar-product-2024-01-15.log`

### 3. فرمت استاندارد
```
[2024-01-15 14:30:25] [SUCCESS] [User: admin] محصول جدید اضافه شد | {"product_id":123,"name":"گردنبند"}
```

### 4. امنیت
- فایل `.htaccess` برای محافظت از لاگ‌ها
- عدم دسترسی مستقیم از خارج
- Nonce برای AJAX requests

### 5. رابط کاربری
- تب‌های مجزا برای هر نوع لاگ
- فیلتر بر اساس سطح (INFO, SUCCESS, WARNING, ERROR)
- بارگذاری با AJAX بدون رفرش صفحه
- نمایش جزئیات Context
- حذف لاگ‌ها

## توصیه‌های بهینه‌سازی

1. **استفاده از Context**
   ```php
   // خوب ✅
   $logger->logProduct('محصول آپدیت شد', AdvancedLogger::LEVEL_INFO, [
       'product_id' => $id,
       'changes' => $changes
   ]);
   
   // بد ❌
   $logger->logProduct("محصول $id آپدیت شد با تغییرات " . json_encode($changes));
   ```

2. **انتخاب سطح مناسب**
   - `INFO`: اطلاع‌رسانی عادی
   - `SUCCESS`: عملیات موفق مهم
   - `WARNING`: مشکلات جزئی که نیاز به توجه دارند
   - `ERROR`: خطاهای جدی

3. **محدود کردن لاگ‌ها**
   ```php
   // در حلقه‌های بزرگ، خلاصه بزنید
   foreach ($products as $product) {
       // به جای لاگ هر محصول، فقط خلاصه در انتها
   }
   $logger->logProduct("$count محصول همگام شد", AdvancedLogger::LEVEL_SUCCESS);
   ```

## پشتیبانی

در صورت بروز مشکل، لاگ‌های Error را بررسی کنید:
```php
$errors = $logger->getLogs(AdvancedLogger::TYPE_ERROR, 50);
```
