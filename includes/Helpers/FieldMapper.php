<?php
/**
 * Field Mapper Helper
 * 
 * نقشه‌برداری و اعمال فیلدهای زرگر به محصولات WooCommerce
 * 
 * @package ZargarAccounting
 * @since 2.0.0
 */

namespace ZargarAccounting\Helpers;

if (!defined('ABSPATH')) {
    exit;
}

class FieldMapper {
    
    /**
     * دریافت لیست فیلدهای قابل import
     */
    public static function getAvailableFields(): array {
        return [
            'basic' => [
                'title' => 'اطلاعات اصلی',
                'fields' => [
                    'ProductId' => ['label' => 'شناسه محصول', 'target' => 'meta:_external_id'],
                    'ProductCode' => ['label' => 'کد محصول (SKU)', 'target' => 'sku'],
                    'ProductTitle' => ['label' => 'عنوان محصول', 'target' => 'post_title'],
                ]
            ],
            'weight' => [
                'title' => 'وزن و اندازه',
                'fields' => [
                    'Weight' => ['label' => 'وزن', 'target' => 'attribute:weight'],
                    'BaseWeight' => ['label' => 'وزن پایه', 'target' => 'attribute:base_weight'],
                    'WeightSymbolRate' => ['label' => 'نرخ وزن', 'target' => 'attribute:_weight_symbol_rate'],
                ]
            ],
            'stock' => [
                'title' => 'موجودی و دسته‌بندی',
                'fields' => [
                    'IsExists' => ['label' => 'وضعیت موجودی', 'target' => 'stock_status'],
                    'CategoryTitle' => ['label' => 'دسته‌بندی', 'target' => 'term:product_cat'],
                    'LocationTitle' => ['label' => 'محل نگهداری', 'target' => 'meta:_location'],
                ]
            ],
            'images' => [
                'title' => 'تصاویر',
                'fields' => [
                    'DefaultImageURL' => ['label' => 'تصویر شاخص', 'target' => 'featured_image'],
                    'ImageURL1' => ['label' => 'تصویر گالری 1', 'target' => 'gallery_image'],
                    'ImageURL2' => ['label' => 'تصویر گالری 2', 'target' => 'gallery_image'],
                    'ImageURL3' => ['label' => 'تصویر گالری 3', 'target' => 'gallery_image'],
                    'ImageURL4' => ['label' => 'تصویر گالری 4', 'target' => 'gallery_image'],
                    'ImageURL5' => ['label' => 'تصویر گالری 5', 'target' => 'gallery_image'],
                    'ImageURL6' => ['label' => 'تصویر گالری 6', 'target' => 'gallery_image'],
                ]
            ],
            'attributes' => [
                'title' => 'ویژگی‌ها',
                'fields' => [
                    'ModelTitle' => ['label' => 'مدل', 'target' => 'attribute:model'],
                    'ColorTitle' => ['label' => 'رنگ', 'target' => 'attribute:color'],
                    'SizeTitle' => ['label' => 'سایز', 'target' => 'attribute:size'],
                    'CollectionTitle' => ['label' => 'مجموعه', 'target' => 'attribute:collection'],
                    'calculated_wage_percent' => ['label' => 'اجرت(درصد)', 'target' => 'attribute:wagepercent'],
                ]
            ],
            'pricing' => [
                'title' => 'قیمت‌گذاری',
                'fields' => [
                    'GoldPrice' => ['label' => 'قیمت طلا', 'target' => 'regular_price'],
                    'StonePrice' => ['label' => 'قیمت سنگ', 'target' => 'meta:_stone_price'],
                    'TotalPrice' => ['label' => 'قیمت نهایی', 'target' => 'sale_price'],
                ]
            ],
            'tax' => [
                'title' => 'مالیات و درآمد',
                'fields' => [
                    'TaxPercent' => ['label' => 'درصد مالیات', 'target' => 'tax_class'],
                    'IncomeTotal' => ['label' => 'مجموع درآمد', 'target' => 'meta:_income_total'],
                    'TaxTotal' => ['label' => 'مجموع مالیات', 'target' => 'meta:_tax_total'],
                ]
            ],
            'sales' => [
                'title' => 'اجرت فروش',
                'fields' => [
                    'SaleWageOfPercent' => ['label' => 'درصد اجرت فروش', 'target' => 'meta:sale_wage_percent'],
                    'SaleWageOfPrice' => ['label' => 'مبلغ اجرت فروش', 'target' => 'meta:sale_wage_price'],
                    'SaleWageOfPriceType' => ['label' => 'نوع اجرت فروش', 'target' => 'meta:sale_wage_price_type'],
                    'SaleStonePrice' => ['label' => 'اجرت سنگ فروش', 'target' => 'meta:sale_wage_stone'],
                ]
            ],
            'codes' => [
                'title' => 'کدهای اضافی',
                'fields' => [
                    'OfficeCode' => ['label' => 'کد دفتر', 'target' => 'meta:_office_code'],
                    'DesignerCode' => ['label' => 'کد طراح', 'target' => 'meta:_designer_code'],
                    'other1' => ['label' => 'فیلد اضافه ۱', 'target' => 'meta:_extra_field_1'],
                    'other2' => ['label' => 'فیلد اضافه ۲', 'target' => 'meta:_extra_field_2'],
                ]
            ],
        ];
    }
    
    /**
     * اعمال فیلدهای انتخاب شده به محصول
     */
    public function applyFieldsToProduct(\WC_Product $product, array $productData, array $selectedFields): void {
        // محاسبه اجرت درصدی
        if (in_array('calculated_wage_percent', $selectedFields)) {
            $productData['calculated_wage_percent'] = $this->calculateWagePercent($productData);
        }
        
        foreach ($selectedFields as $fieldName) {
            if (!isset($productData[$fieldName])) {
                continue;
            }
            
            $value = $productData[$fieldName];
            $target = $this->getFieldTarget($fieldName);
            
            if (!$target) {
                continue;
            }
            
            $this->applyField($product, $target, $value, $fieldName, $productData);
        }
    }
    
    /**
     * محاسبه اجرت درصدی
     * فرمول: (اجرت ساخت / قیمت طلا) × 100
     */
    private function calculateWagePercent(array $productData): float {
        $goldPrice = floatval($productData['GoldPrice'] ?? 0);
        $wageOfPrice = floatval($productData['WageOfPrice'] ?? 0);
        
        if ($goldPrice <= 0) {
            return 0.0;
        }
        
        $wagePercent = ($wageOfPrice / $goldPrice) * 100;
        return round($wagePercent, 1);
    }
    
    /**
     * دریافت target یک فیلد
     */
    private function getFieldTarget(string $fieldName): ?string {
        $allFields = self::getAvailableFields();
        
        foreach ($allFields as $category) {
            if (isset($category['fields'][$fieldName])) {
                return $category['fields'][$fieldName]['target'];
            }
        }
        
        return null;
    }
    
    /**
     * اعمال یک فیلد به محصول
     */
    private function applyField(\WC_Product $product, string $target, $value, string $fieldName, array $productData): void {
        // Meta field
        if (strpos($target, 'meta:') === 0) {
            $metaKey = str_replace('meta:', '', $target);
            $product->update_meta_data($metaKey, $value);
            return;
        }
        
        // Attribute
        if (strpos($target, 'attribute:') === 0) {
            $attrName = str_replace('attribute:', '', $target);
            $this->setProductAttribute($product, $attrName, $value);
            return;
        }
        
        // Term (Category)
        if (strpos($target, 'term:') === 0) {
            $taxonomy = str_replace('term:', '', $target);
            $this->setProductTerm($product, $taxonomy, $value);
            return;
        }
        
        // Gallery Image
        if ($target === 'gallery_image') {
            $this->addGalleryImage($product, $value);
            return;
        }
        
        // Featured Image
        if ($target === 'featured_image') {
            $this->setFeaturedImage($product, $value);
            return;
        }
        
        // Direct WooCommerce fields
        switch ($target) {
            case 'sku':
                $product->set_sku($value);
                break;
            case 'post_title':
                $product->set_name($value);
                break;
            case 'regular_price':
                $product->set_regular_price($value);
                break;
            case 'sale_price':
                $product->set_sale_price($value);
                break;
            case 'stock_status':
                $status = $value ? 'instock' : 'outofstock';
                $product->set_stock_status($status);
                break;
            case 'tax_class':
                $product->set_tax_class($value);
                break;
        }
    }
    
    /**
     * تنظیم attribute محصول - استفاده از attribute های از قبل ساخته شده
     */
    private function setProductAttribute(\WC_Product $product, string $attrName, $value): void {
        if (empty($value)) {
            return;
        }
        
        $attributes = $product->get_attributes();
        
        // بررسی اینکه آیا attribute از قبل به عنوان taxonomy وجود دارد
        $taxonomy_name = 'pa_' . $attrName;
        
        if (taxonomy_exists($taxonomy_name)) {
            // استفاده از global attribute
            $term = get_term_by('name', $value, $taxonomy_name);
            
            if (!$term) {
                // اگر term وجود نداشت، ایجاد کن
                $term_data = wp_insert_term($value, $taxonomy_name);
                if (!is_wp_error($term_data)) {
                    $term_id = $term_data['term_id'];
                } else {
                    // اگر خطا داشت، از attribute محلی استفاده کن
                    $this->setLocalAttribute($product, $attributes, $attrName, $value);
                    return;
                }
            } else {
                $term_id = $term->term_id;
            }
            
            // Set term برای محصول
            wp_set_object_terms($product->get_id(), (int)$term_id, $taxonomy_name, true);
            
            // اضافه کردن attribute به محصول
            $attribute = new \WC_Product_Attribute();
            $attribute->set_id(wc_attribute_taxonomy_id_by_name($taxonomy_name));
            $attribute->set_name($taxonomy_name);
            $attribute->set_options([(int)$term_id]);
            $attribute->set_visible(true);
            $attribute->set_variation(false);
            
            $attributes[$taxonomy_name] = $attribute;
        } else {
            // استفاده از local/custom attribute
            $this->setLocalAttribute($product, $attributes, $attrName, $value);
        }
        
        $product->set_attributes($attributes);
    }
    
    /**
     * تنظیم attribute محلی (غیر global)
     */
    private function setLocalAttribute(\WC_Product $product, array &$attributes, string $attrName, $value): void {
        $attribute = new \WC_Product_Attribute();
        $attribute->set_name($attrName);
        $attribute->set_options([$value]);
        $attribute->set_visible(true);
        $attribute->set_variation(false);
        
        $attributes[$attrName] = $attribute;
    }
    
    /**
     * تنظیم term (دسته‌بندی)
     */
    private function setProductTerm(\WC_Product $product, string $taxonomy, string $termName): void {
        if (empty($termName)) {
            return;
        }
        
        $term = term_exists($termName, $taxonomy);
        
        if (!$term) {
            $term = wp_insert_term($termName, $taxonomy);
        }
        
        if (!is_wp_error($term)) {
            $termId = is_array($term) ? $term['term_id'] : $term;
            wp_set_object_terms($product->get_id(), $termId, $taxonomy);
        }
    }
    
    /**
     * افزودن تصویر به گالری
     */
    private function addGalleryImage(\WC_Product $product, string $imageUrl): void {
        if (empty($imageUrl)) {
            return;
        }
        
        // اطمینان از ذخیره محصول قبل از اضافه کردن تصویر
        if (!$product->get_id()) {
            $product->save();
        }
        
        $attachmentId = $this->downloadAndAttachImage($imageUrl, $product->get_id());
        
        if ($attachmentId) {
            $galleryIds = $product->get_gallery_image_ids();
            $galleryIds[] = $attachmentId;
            $product->set_gallery_image_ids($galleryIds);
        }
    }
    
    /**
     * تنظیم تصویر شاخص
     */
    private function setFeaturedImage(\WC_Product $product, string $imageUrl): void {
        if (empty($imageUrl)) {
            return;
        }
        
        // اطمینان از ذخیره محصول قبل از اضافه کردن تصویر
        if (!$product->get_id()) {
            $product->save();
        }
        
        $attachmentId = $this->downloadAndAttachImage($imageUrl, $product->get_id());
        
        if ($attachmentId) {
            $product->set_image_id($attachmentId);
        }
    }
    
    /**
     * دانلود و ضمیمه کردن تصویر
     */
    private function downloadAndAttachImage(string $imageUrl, int $productId = 0): ?int {
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        
        // اجازه دادن به دانلود از HTTP (نه فقط HTTPS)
        add_filter('https_ssl_verify', '__return_false');
        add_filter('https_local_ssl_verify', '__return_false');
        add_filter('http_request_host_is_external', '__return_true');
        
        // بررسی اینکه آیا تصویر قبلاً دانلود شده
        $existing = $this->findExistingAttachment(basename($imageUrl));
        if ($existing) {
            error_log('Zargar Image: Using existing attachment ID ' . $existing . ' | URL: ' . $imageUrl);
            return $existing;
        }
        
        // تنظیمات دانلود برای HTTP
        $tmp = download_url($imageUrl, 300, false);
        
        if (is_wp_error($tmp)) {
            error_log('Zargar Image Download Error: ' . $tmp->get_error_message() . ' | URL: ' . $imageUrl);
            
            // سعی دوم با curl
            $tmp = $this->downloadImageWithCurl($imageUrl);
            if (!$tmp) {
                return null;
            }
        }
        
        $fileName = basename($imageUrl);
        
        $file = [
            'name' => $fileName,
            'tmp_name' => $tmp
        ];
        
        $attachmentId = media_handle_sideload($file, $productId);
        
        if (is_wp_error($attachmentId)) {
            error_log('Zargar Image Sideload Error: ' . $attachmentId->get_error_message() . ' | File: ' . $fileName);
            @unlink($tmp);
            return null;
        }
        
        error_log('Zargar Image Success: Attached ID ' . $attachmentId . ' | Product: ' . $productId . ' | URL: ' . $imageUrl);
        return $attachmentId;
    }
    
    /**
     * دانلود تصویر با cURL (برای مواقعی که download_url کار نمی‌کند)
     */
    private function downloadImageWithCurl(string $imageUrl): ?string {
        if (!function_exists('curl_init')) {
            error_log('Zargar Image: cURL not available');
            return null;
        }
        
        $ch = curl_init($imageUrl);
        $tmpFile = wp_tempnam($imageUrl);
        
        $fp = fopen($tmpFile, 'wb');
        
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        
        $success = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        curl_close($ch);
        fclose($fp);
        
        if (!$success || $httpCode !== 200) {
            error_log('Zargar Image cURL Error: HTTP ' . $httpCode . ' | URL: ' . $imageUrl);
            @unlink($tmpFile);
            return null;
        }
        
        error_log('Zargar Image: Downloaded with cURL | URL: ' . $imageUrl);
        return $tmpFile;
    }
    
    /**
     * جستجوی تصویر موجود با نام فایل
     */
    private function findExistingAttachment(string $filename): ?int {
        global $wpdb;
        
        $attachment_id = $wpdb->get_var($wpdb->prepare(
            "SELECT ID FROM {$wpdb->posts} WHERE post_type = 'attachment' AND guid LIKE %s LIMIT 1",
            '%' . $wpdb->esc_like($filename)
        ));
        
        return $attachment_id ? (int)$attachment_id : null;
    }
}
