# راهنمای تست و نگاشت محصولات زرگر

## اجرای صفحات آزمایشی

```bash
cd ~/Documents/zargar-accounting
php -S localhost:8000 -t sandbox
```

سپس در مرورگر:

- `http://localhost:8000/import-runner.php` → نمایش داده‌ی خام و خروجی map.
- `http://localhost:8000/import-review.php` → جدول نگاشت با ستون‌های (فیلد API، مقصد، عنوان فارسی، بله/خیر).

## متادیتاهای لازم (Meta Fields)

| متا | توضیح |
| --- | --- |
| `_location` | محل نگهداری |
| `_external_id` | شناسه خارجی |
| `_stone_price` | قیمت سنگ |
| `wage_price` | اجرت |
| `_income_total` | مجموع درآمد |
| `_tax_total` | مجموع مالیات |
| `sale_wage_percent` | درصد اجرت فروش |
| `sale_wage_price` | مبلغ اجرت فروش |
| `sale_wage_price_type` | نوع اجرت فروش |
| `sale_wage_stone` | اجرت سنگ فروش |
| `_office_code` | کد دفتر |
| `_designer_code` | کد طراح |
| `_extra_field_1` | فیلد اضافی ۱ |
| `_extra_field_2` | فیلد اضافی ۲ |

## ویژگی‌های محصول (Attributes)

| اسلاگ | عنوان |
| --- | --- |
| `_base_weight` | وزن پایه |
| `weight` | وزن |
| `model` | مدل |
| `collection` | مجموعه |
| `pa_color` | رنگ |
| `pa_size` | سایز |
| `weight_symbol_rate` | نرخ وزن |

## فایل‌های مهم

- `sandbox/importproduct.php` → تنظیمات `get_field_mapping_config` و توابع نگاشت.
- `sandbox/import-review.php` → جدول UI بررسی فیلدها.
- `sandbox/import-runner.php` → تست نگاشت با داده‌ی نمونه.
- `sandbox/setup-zargar-fields.php` → ثبت متادیتا و Attributeها در WooCommerce (با `wp eval-file`).
