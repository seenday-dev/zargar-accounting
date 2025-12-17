<?php
declare(strict_types=1);

/**
 * Product Import Field Mapping Configuration
 * 
 * برای هر فیلد می‌توانید مشخص کنید:
 * - enabled: آیا این فیلد import شود؟ (true/false)
 * - target: فیلد مقصد در WooCommerce
 * - label: عنوان فارسی فیلد
 * 
 * @package ZargarAccounting
 * @since 1.0.0
 */

/**
 * تنظیمات نقشه‌برداری فیلدها
 * برای غیرفعال کردن import هر فیلد، enabled را false کنید
 */
function get_field_mapping_config(): array {
    return [
        // اطلاعات اصلی محصول
        'ProductId' => [
            'enabled' => true,
            'target' => 'meta:external_id',
            'label' => 'شناسه محصول'
        ],
        'ProductCode' => [
            'enabled' => true,
            'target' => 'sku',
            'label' => 'کد محصول (SKU)'
        ],
        'ProductTitle' => [
            'enabled' => true,
            'target' => 'post_title',
            'label' => 'عنوان محصول'
        ],
        
        // وزن و اندازه
        'Weight' => [
            'enabled' => true,
            'target' => 'attribute:weight',
            'label' => 'وزن'
        ],
        'BaseWeight' => [
            'enabled' => true,
            'target' => 'attribute:base_weight',
            'label' => 'وزن پایه'
        ],
        'WeightSymbolRate' => [
            'enabled' => true,
            'target' => 'attribute:_weight_symbol_rate',
            'label' => 'نرخ نماد وزن'
        ],
        
        // موجودی و دسته‌بندی
        'IsExists' => [
            'enabled' => true,
            'target' => 'stock_status',
            'label' => 'وضعیت موجودی'
        ],
        'CategoryTitle' => [
            'enabled' => true,
            'target' => 'term:product_cat',
            'label' => 'دسته‌بندی'
        ],
        'LocationTitle' => [
            'enabled' => true,
            'target' => 'meta:location',
            'label' => 'مکان'
        ],
        
        // تصاویر گالری
        'ImageURL1' => [
            'enabled' => true,
            'target' => 'gallery_image',
            'label' => 'تصویر گالری 1'
        ],
        'ImageURL2' => [
            'enabled' => true,
            'target' => 'gallery_image',
            'label' => 'تصویر گالری 2'
        ],
        'ImageURL3' => [
            'enabled' => true,
            'target' => 'gallery_image',
            'label' => 'تصویر گالری 3'
        ],
        'ImageURL4' => [
            'enabled' => true,
            'target' => 'gallery_image',
            'label' => 'تصویر گالری 4'
        ],
        'ImageURL5' => [
            'enabled' => true,
            'target' => 'gallery_image',
            'label' => 'تصویر گالری 5'
        ],
        'ImageURL6' => [
            'enabled' => true,
            'target' => 'gallery_image',
            'label' => 'تصویر گالری 6'
        ],
        'DefaultImageURL' => [
            'enabled' => true,
            'target' => 'featured_image',
            'label' => 'تصویر شاخص'
        ],
        
        // ویژگی‌های محصول
        'ModelTitle' => [
            'enabled' => true,
            'target' => 'attribute:model',
            'label' => 'مدل'
        ],
        'ColorTitle' => [
            'enabled' => true,
            'target' => 'attribute:color',
            'label' => 'رنگ'
        ],
        'SizeTitle' => [
            'enabled' => true,
            'target' => 'attribute:size',
            'label' => 'سایز'
        ],
        'CollectionTitle' => [
            'enabled' => true,
            'target' => 'attribute:collection',
            'label' => 'کلکسیون'
        ],
        
        // قیمت‌گذاری
        'GoldPrice' => [
            'enabled' => true,
            'target' => 'regular_price',
            'label' => 'قیمت طلا'
        ],
        'StonePrice' => [
            'enabled' => true,
            'target' => 'meta:stone_price',
            'label' => 'قیمت سنگ'
        ],
        'WageOfPrice' => [
            'enabled' => true,
            'target' => 'meta:wage_price',
            'label' => 'اجرت'
        ],
        'TotalPrice' => [
            'enabled' => true,
            'target' => 'sale_price',
            'label' => 'قیمت نهایی'
        ],
        
        // مالیات و درآمد
        'TaxPercent' => [
            'enabled' => true,
            'target' => 'tax_class',
            'label' => 'درصد مالیات'
        ],
        'IncomeTotal' => [
            'enabled' => true,
            'target' => 'meta:income_total',
            'label' => 'مجموع درآمد'
        ],
        'TaxTotal' => [
            'enabled' => true,
            'target' => 'meta:tax_total',
            'label' => 'مجموع مالیات'
        ],
        
        // اجرت فروش
        'SaleWageOfPercent' => [
            'enabled' => true,
            'target' => 'meta:sale_wage_percent',
            'label' => 'درصد اجرت فروش'
        ],
        'SaleWageOfPrice' => [
            'enabled' => true,
            'target' => 'meta:sale_wage_price',
            'label' => 'مبلغ اجرت فروش'
        ],
        'SaleWageOfPriceType' => [
            'enabled' => true,
            'target' => 'meta:sale_wage_price_type',
            'label' => 'نوع اجرت فروش'
        ],
        'SaleStonePrice' => [
            'enabled' => true,
            'target' => 'meta:sale_wage_stone',
            'label' => 'قیمت سنگ فروش'
        ],
        
        // کدهای اضافی
        'OldCode' => [
            'enabled' => true,
            'target' => 'meta:old_code',
            'label' => 'کد قدیمی'
        ],
        'OfficeCode' => [
            'enabled' => true,
            'target' => 'meta:office_code',
            'label' => 'کد دفتر'
        ],
        'DesignerCode' => [
            'enabled' => true,
            'target' => 'meta:designer_code',
            'label' => 'کد طراح'
        ],
        
        // فیلدهای دلخواه
        'other1' => [
            'enabled' => false, // غیرفعال به صورت پیش‌فرض
            'target' => 'meta:extra_field_1',
            'label' => 'فیلد اضافی 1'
        ],
        'other2' => [
            'enabled' => false, // غیرفعال به صورت پیش‌فرض
            'target' => 'meta:extra_field_2',
            'label' => 'فیلد اضافی 2'
        ],
    ];
}

/**
 * دریافت فیلد مقصد WooCommerce برای یک فیلد زرگر
 * فقط فیلدهای فعال (enabled = true) برگردانده می‌شوند
 * 
 * @param string $field نام فیلد زرگر
 * @return string|null فیلد مقصد یا null اگر غیرفعال باشد
 */
function map_zargar_field(string $field): ?string {
    static $config = null;
    
    if ($config === null) {
        $config = get_field_mapping_config();
    }
    
    // اگر فیلد وجود ندارد
    if (!isset($config[$field])) {
        return null;
    }
    
    // اگر فیلد غیرفعال است
    if (!$config[$field]['enabled']) {
        return null;
    }
    
    return $config[$field]['target'];
}

/**
 * دریافت لیست فیلدهای فعال
 * 
 * @return array لیست نام فیلدهای فعال
 */
function get_enabled_fields(): array {
    $config = get_field_mapping_config();
    $enabled = [];
    
    foreach ($config as $field => $settings) {
        if ($settings['enabled']) {
            $enabled[] = $field;
        }
    }
    
    return $enabled;
}

/**
 * دریافت لیست فیلدهای غیرفعال
 * 
 * @return array لیست نام فیلدهای غیرفعال
 */
function get_disabled_fields(): array {
    $config = get_field_mapping_config();
    $disabled = [];
    
    foreach ($config as $field => $settings) {
        if (!$settings['enabled']) {
            $disabled[] = $field;
        }
    }
    
    return $disabled;
}

/**
 * تبدیل داده‌های محصول زرگر به فیلدهای WooCommerce
 * فقط فیلدهای فعال import می‌شوند
 * 
 * @param array $zargarProduct داده‌های محصول از API زرگر
 * @return array داده‌های نقشه‌برداری شده برای WooCommerce
 */
function map_product_data(array $zargarProduct): array {
    $mapped = [];
    $config = get_field_mapping_config();
    
    foreach ($zargarProduct as $key => $value) {
        // بررسی وجود فیلد در تنظیمات
        if (!isset($config[$key])) {
            continue;
        }
        
        // بررسی فعال بودن فیلد
        if (!$config[$key]['enabled']) {
            continue;
        }
        
        $target = $config[$key]['target'];
        $mapped[$target] = $value;
    }
    
    return $mapped;
}

/**
 * دریافت تنظیمات یک فیلد خاص
 * 
 * @param string $field نام فیلد
 * @return array|null تنظیمات فیلد یا null
 */
function get_field_config(string $field): ?array {
    $config = get_field_mapping_config();
    return $config[$field] ?? null;
}

/**
 * تغییر وضعیت یک فیلد (فعال/غیرفعال)
 * این تابع برای استفاده در رابط کاربری است
 * 
 * @param string $field نام فیلد
 * @param bool $enabled وضعیت جدید
 * @return bool موفقیت عملیات
 */
function toggle_field_status(string $field, bool $enabled): bool {
    // این تابع باید با WordPress Options API کار کند
    // برای ذخیره تنظیمات کاربر
    
    $user_config = get_option('zargar_field_mapping', []);
    $user_config[$field] = $enabled;
    
    return update_option('zargar_field_mapping', $user_config);
}

/**
 * نمایش گزارش فیلدهای فعال/غیرفعال
 * برای دیباگ و بررسی
 */
function print_field_status_report(): void {
    $config = get_field_mapping_config();
    
    echo "=== گزارش وضعیت فیلدها ===\n\n";
    
    echo "فیلدهای فعال:\n";
    echo str_repeat('-', 50) . "\n";
    foreach ($config as $field => $settings) {
        if ($settings['enabled']) {
            printf("✅ %s → %s (%s)\n", 
                $field, 
                $settings['target'], 
                $settings['label']
            );
        }
    }
    
    echo "\n";
    echo "فیلدهای غیرفعال:\n";
    echo str_repeat('-', 50) . "\n";
    foreach ($config as $field => $settings) {
        if (!$settings['enabled']) {
            printf("❌ %s → %s (%s)\n", 
                $field, 
                $settings['target'], 
                $settings['label']
            );
        }
    }
    
    echo "\n";
    echo sprintf("📊 جمع کل: %d فیلد (%d فعال، %d غیرفعال)\n",
        count($config),
        count(get_enabled_fields()),
        count(get_disabled_fields())
    );
}

// مثال استفاده:
if (php_sapi_name() === 'cli') {
    print_field_status_report();
}
