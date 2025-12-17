# راهنمای تفکیک Meta vs Attribute

## فیلدهای Meta (14 فیلد)
این فیلدها در `post_meta` ذخیره می‌شوند و در Meta Box محصول نمایش داده می‌شوند:

1. `_location` - محل نگهداری
2. `_external_id` - شناسه خارجی  
3. `_stone_price` - قیمت سنگ
4. `wage_price` - قیمت اجرت
5. `_income_total` - مجموع درآمد
6. `_tax_total` - مجموع مالیات
7. `sale_wage_percent` - درصد اجرت فروش
8. `sale_wage_price` - مبلغ اجرت فروش
9. `sale_wage_price_type` - نوع اجرت فروش
10. `sale_wage_stone` - اجرت سنگ فروش
11. `_office_code` - کد دفتر
12. `_designer_code` - کد طراح
13. `_extra_field_1` - فیلد اضافی 1
14. `_extra_field_2` - فیلد اضافی 2

## فیلدهای Attribute (8 فیلد)
این فیلدها به عنوان WooCommerce Product Attributes ذخیره می‌شوند:

1. `weight` - وزن
2. `base_weight` - وزن پایه
3. `model` - مدل
4. `collection` - مجموعه
5. `color` - رنگ
6. `size` - سایز
7. `_weight_symbol_rate` - نرخ وزن
8. `wagepercent` - درصد اجرت (محاسباتی)

## تصاویر
تمام تصاویر اکنون با URL کامل ذخیره می‌شوند:
- قبل: `/files/Img20251110-254.jpg`
- بعد: `http://37.235.18.235:8090/files/Img20251110-254.jpg`

## فیلدهای استاندارد WooCommerce
- `ProductCode` → SKU
- `ProductTitle` → عنوان محصول
- `GoldPrice` → قیمت عادی
- `TotalPrice` → قیمت فروش
- `IsExists` → وضعیت موجودی
- `CategoryTitle` → دسته‌بندی

## تست
برای تست ایمپورت یک محصول:
```
http://localhost/wp/wp-content/plugins/zargar-accounting/test-import-single.php?code=GD01000312
```

## کد نمونه
```php
use ZargarAccounting\Admin\ProductImportManager;
use ZargarAccounting\Helpers\FieldMapper;

$manager = ProductImportManager::getInstance();

// جستجو
$products = $manager->searchProductsByCodes(['GD01000312']);

// انتخاب فیلدها
$allFields = FieldMapper::getAvailableFields();
$selectedFields = [];
foreach ($allFields as $category) {
    foreach ($category['fields'] as $fieldName => $info) {
        $selectedFields[] = $fieldName;
    }
}

// ایمپورت
$manager->importSingleProduct($products[0], $selectedFields);
```
