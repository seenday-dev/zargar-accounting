# راهنمای تست

## اجرای تست‌ها

### روش 1: استفاده از PHPUnit (توصیه می‌شود)

```bash
# اجرای تمام تست‌ها
./vendor/bin/phpunit

# اجرای تست‌ها با خروجی تمیز
./vendor/bin/phpunit --testdox

# اجرای یک فایل تست خاص
./vendor/bin/phpunit tests/MonologManagerTest.php

# اجرای با coverage report
./vendor/bin/phpunit --coverage-html coverage
```

### روش 2: استفاده از Composer Script

شما می‌توانید یک script در `composer.json` اضافه کنید:

```json
{
    "scripts": {
        "test": "./vendor/bin/phpunit --testdox",
        "test:coverage": "./vendor/bin/phpunit --coverage-html coverage"
    }
}
```

سپس اجرا کنید:

```bash
composer test
```

### روش 3: استفاده از Test Runner ساده

```bash
php tests/run-tests.php
```

## تست‌های موجود

### 1. MonologManagerTest
تست کامل سیستم لاگ با Monolog:
- Singleton pattern
- تمام متدهای logging (product, sales, price, error, etc.)
- خواندن و پاک کردن لاگ‌ها
- آمارگیری

### 2. LoggerAjaxTest
تست AJAX handler:
- Singleton pattern
- وجود متدهای مورد نیاز

### 3. BladeRendererTest
تست سیستم template:
- Singleton pattern

## افزودن تست جدید

برای افزودن تست جدید:

1. فایل تست را در پوشه `tests/` بسازید
2. از نام‌گذاری `*Test.php` استفاده کنید
3. کلاس را از `PHPUnit\Framework\TestCase` extend کنید

مثال:

```php
<?php

use PHPUnit\Framework\TestCase;

class MyNewTest extends TestCase {
    public function testSomething() {
        $this->assertTrue(true);
    }
}
```

## دستورات مفید

```bash
# اجرای با verbose mode
./vendor/bin/phpunit --verbose

# اجرای فقط یک تست خاص
./vendor/bin/phpunit --filter testProductLogging

# اجرای با stop on failure
./vendor/bin/phpunit --stop-on-failure

# نمایش لیست تست‌ها بدون اجرا
./vendor/bin/phpunit --list-tests
```

## CI/CD Integration

برای استفاده در GitHub Actions یا GitLab CI:

```yaml
test:
  script:
    - composer install
    - ./vendor/bin/phpunit --testdox
```

## نکات مهم

- تست‌ها از WordPress مستقل هستند و نیازی به نصب WordPress ندارند
- تمام توابع WordPress در `tests/bootstrap.php` mock شده‌اند
- فایل‌های لاگ در حین تست واقعاً ساخته می‌شوند (در `storage/logs/`)
- برای تست‌های تمیز، می‌توانید لاگ‌ها را پاک کنید

---

**آخرین بروزرسانی:** 2025-12-15
