<?php
/**
 * تست ایمپورت یک محصول خاص
 * برای تست: http://localhost/wp/wp-content/plugins/zargar-accounting/test-import-single.php
 */

// Load WordPress
define('WP_USE_THEMES', false);
require_once('../../../wp-load.php');

// Check if WooCommerce is active
if (!class_exists('WooCommerce')) {
    die('WooCommerce is not active!');
}

use ZargarAccounting\Admin\ProductImportManager;
use ZargarAccounting\Helpers\FieldMapper;

echo "<h1>تست ایمپورت محصول GD01000312</h1>";
echo "<pre>";

// دریافت کد محصول از URL
$productCode = isset($_GET['code']) ? sanitize_text_field($_GET['code']) : 'GD01000312';

echo "در حال جستجوی محصول: $productCode\n";
echo str_repeat('-', 50) . "\n\n";

$manager = ProductImportManager::getInstance();

try {
    // جستجوی محصول
    $products = $manager->searchProductsByCodes([$productCode]);
    
    if (empty($products)) {
        die("محصول یافت نشد!");
    }
    
    $productData = $products[0];
    
    echo "محصول یافت شد:\n";
    echo "- کد: " . ($productData['ProductCode'] ?? 'N/A') . "\n";
    echo "- نام: " . ($productData['ProductTitle'] ?? 'N/A') . "\n";
    echo "- وزن: " . ($productData['Weight'] ?? 'N/A') . "\n";
    echo "- مدل: " . ($productData['ModelTitle'] ?? 'N/A') . "\n";
    echo "- رنگ: " . ($productData['ColorTitle'] ?? 'N/A') . "\n";
    echo "- قیمت طلا: " . ($productData['GoldPrice'] ?? 'N/A') . "\n";
    echo "- قیمت کل: " . ($productData['TotalPrice'] ?? 'N/A') . "\n";
    echo "- اجرت: " . ($productData['WageOfPrice'] ?? 'N/A') . "\n";
    
    // محاسبه اجرت درصدی
    $goldPrice = floatval($productData['GoldPrice'] ?? 0);
    $wageOfPrice = floatval($productData['WageOfPrice'] ?? 0);
    $wagePercent = $goldPrice > 0 ? round(($wageOfPrice / $goldPrice) * 100, 1) : 0;
    echo "- اجرت درصدی (محاسبه شده): " . $wagePercent . "%\n";
    
    echo "- تصویر: " . ($productData['DefaultImageURL'] ?? 'N/A') . "\n";
    echo "\n" . str_repeat('-', 50) . "\n\n";
    
    // دریافت تمام فیلدهای موجود
    $allFields = FieldMapper::getAvailableFields();
    $selectedFields = [];
    
    // انتخاب تمام فیلدها
    foreach ($allFields as $category) {
        foreach ($category['fields'] as $fieldName => $fieldInfo) {
            $selectedFields[] = $fieldName;
        }
    }
    
    echo "فیلدهای انتخاب شده: " . count($selectedFields) . "\n";
    echo implode(', ', $selectedFields) . "\n\n";
    
    // بررسی تصاویر در داده API
    echo "تصاویر در داده API:\n";
    $imageFields = ['DefaultImageURL', 'ImageURL1', 'ImageURL2', 'ImageURL3', 'ImageURL4', 'ImageURL5', 'ImageURL6'];
    foreach ($imageFields as $imgField) {
        if (!empty($productData[$imgField])) {
            echo "- $imgField: " . $productData[$imgField] . "\n";
        }
    }
    echo "\n" . str_repeat('-', 50) . "\n\n";
    
    // ایمپورت محصول
    echo "در حال ایمپورت...\n";
    $manager->importSingleProduct($productData, $selectedFields);
    
    echo "\n✅ ایمپورت با موفقیت انجام شد!\n\n";
    
    // یافتن محصول ایمپورت شده
    $sku = $productData['ProductCode'];
    $product_id = wc_get_product_id_by_sku($sku);
    
    if ($product_id) {
        $product = wc_get_product($product_id);
        
        echo "اطلاعات محصول ایمپورت شده:\n";
        echo "- ID: $product_id\n";
        echo "- SKU: " . $product->get_sku() . "\n";
        echo "- نام: " . $product->get_name() . "\n";
        echo "- قیمت عادی: " . $product->get_regular_price() . "\n";
        echo "- قیمت فروش: " . $product->get_sale_price() . "\n";
        echo "- وضعیت موجودی: " . $product->get_stock_status() . "\n";
        echo "\n";
        
        echo "Meta Fields:\n";
        $metaFields = ['_location', '_external_id', '_stone_price', 'wage_price', 
                       '_income_total', '_tax_total', 'sale_wage_percent', 
                       'sale_wage_price', 'sale_wage_price_type', 'sale_wage_stone',
                       '_office_code', '_designer_code', '_extra_field_1', '_extra_field_2'];
        
        foreach ($metaFields as $metaKey) {
            $value = $product->get_meta($metaKey);
            if ($value) {
                echo "- $metaKey: $value\n";
            }
        }
        
        echo "\nAttributes:\n";
        $attributes = $product->get_attributes();
        foreach ($attributes as $attrName => $attribute) {
            $options = $attribute->get_options();
            echo "- $attrName: " . implode(', ', $options) . "\n";
        }
        
        echo "\nتصاویر:\n";
        $imageId = $product->get_image_id();
        if ($imageId) {
            echo "- تصویر شاخص: " . wp_get_attachment_url($imageId) . "\n";
        }
        
        $galleryIds = $product->get_gallery_image_ids();
        if (!empty($galleryIds)) {
            echo "- گالری (" . count($galleryIds) . " تصویر):\n";
            foreach ($galleryIds as $gid) {
                echo "  - " . wp_get_attachment_url($gid) . "\n";
            }
        }
        
        echo "\n" . str_repeat('=', 50) . "\n";
        echo "لینک محصول: " . admin_url("post.php?post=$product_id&action=edit") . "\n";
    }
    
} catch (Exception $e) {
    echo "\n❌ خطا: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "</pre>";
