# ✅ خلاصه تغییرات - نسخه نهایی

## 🎯 مشکلات حل شده

### 1. ❌ مشکل: Attributes به صورت Local ساخته می‌شدند
**✅ راه‌حل:**
- متد `setProductAttribute()` بازنویسی شد
- حالا ابتدا بررسی می‌کند آیا `pa_{attribute_name}` به عنوان taxonomy وجود دارد
- اگر وجود داشت، از Global Attribute استفاده می‌کند
- اگر وجود نداشت، به عنوان Local Attribute ایجاد می‌کند
- Term ها به درستی به taxonomy اضافه می‌شوند

**کد اصلاح شده:**
```php
// قبل
$attribute->set_name($attrName);
$attribute->set_options([$value]);

// بعد
if (taxonomy_exists('pa_' . $attrName)) {
    // استفاده از Global Attribute
    $term = wp_insert_term($value, 'pa_' . $attrName);
    wp_set_object_terms($product->get_id(), $term_id, 'pa_' . $attrName);
    $attribute->set_id(wc_attribute_taxonomy_id_by_name('pa_' . $attrName));
}
```

---

### 2. ❌ مشکل: تصاویر ایمپورت نمی‌شدند
**✅ راه‌حل:**

**مشکل 1:** محصول باید قبل از اضافه کردن تصویر ذخیره شود
```php
// اضافه شد
if (!$product->get_id()) {
    $product->save();
}
```

**مشکل 2:** `media_handle_sideload` باید `product_id` دریافت کند
```php
// قبل
$attachmentId = media_handle_sideload($file, 0);

// بعد
$attachmentId = media_handle_sideload($file, $productId);
```

**مشکل 3:** تصاویر تکراری دانلود می‌شدند
```php
// اضافه شد
$existing = $this->findExistingAttachment(basename($imageUrl));
if ($existing) {
    return $existing;
}
```

**مشکل 4:** عدم وجود logging برای debug
```php
// اضافه شد
error_log('Zargar Image Success: Attached ID ' . $attachmentId);
error_log('Zargar Image Download Error: ' . $error);
```

---

### 3. ❌ مشکل: URL تصاویر ناقص بود
**✅ راه‌حل:**
در `searchProductsByCodes()` تصاویر با URL کامل برگردانده می‌شوند:

```php
$imageFields = ['DefaultImageURL', 'ImageURL1', ...];
foreach ($imageFields as $field) {
    if (!empty($product[$field])) {
        $product[$field] = rtrim($baseUri, '/') . '/' . ltrim($product[$field], '/');
    }
}
```

**نتیجه:**
```
قبل: "/files/Img20251110-254.jpg"
بعد: "http://37.235.18.235:8090/files/Img20251110-254.jpg"
```

---

## 📁 فایل‌های تغییر یافته

### 1. `includes/Helpers/FieldMapper.php`
**تغییرات:**
- ✅ متد `setProductAttribute()` - استفاده از Global Attributes
- ✅ متد `setLocalAttribute()` - اضافه شد برای Local Attributes
- ✅ متد `downloadAndAttachImage()` - اصلاح برای دریافت `$productId`
- ✅ متد `findExistingAttachment()` - اضافه شد برای جلوگیری از تکرار
- ✅ متد `addGalleryImage()` - ذخیره محصول قبل از اضافه کردن تصویر
- ✅ متد `setFeaturedImage()` - ذخیره محصول قبل از اضافه کردن تصویر
- ✅ اضافه کردن logging برای تصاویر

**خطوط تغییر یافته:** 215-374

---

### 2. `includes/Admin/ProductImportManager.php`
**تغییرات:**
- ✅ متد `importSingleProduct()` - تغییر از private به public
- ✅ متد `searchProductsByCodes()` - تغییر از private به public
- ✅ اصلاح URL تصاویر در `searchProductsByCodes()`

**خطوط تغییر یافته:** 404, 472, 537-543

---

## 📄 فایل‌های جدید

### 1. `setup-attributes.php` ⭐
**هدف:** ساخت یکباره 8 Global Attribute

**Attributes ساخته شده:**
1. `pa_weight` - وزن
2. `pa_base_weight` - وزن پایه
3. `pa_wagepercent` - اجرت
4. `pa_size` - سایز
5. `pa_model` - مدل
6. `pa_color` - رنگ
7. `pa_collection` - مجموعه
8. `pa__weight_symbol_rate` - نرخ وزن

**نحوه اجرا:**
```
http://localhost/wp/wp-content/plugins/zargar-accounting/setup-attributes.php
```

**یادآوری:** فقط یکبار باید اجرا شود!

---

### 2. `test-import-single.php`
**هدف:** تست ایمپورت یک محصول خاص

**قابلیت‌ها:**
- جستجوی محصول از API
- نمایش اطلاعات کامل API
- ایمپورت با تمام فیلدها
- نمایش Meta Fields
- نمایش Attributes
- نمایش تصاویر (Featured + Gallery)
- لینک مستقیم به صفحه ویرایش محصول

**نحوه اجرا:**
```
http://localhost/wp/wp-content/plugins/zargar-accounting/test-import-single.php?code=GD01000312
```

---

### 3. `IMPORT-GUIDE.md`
**هدف:** مستندات کامل فارسی

**محتوا:**
- مراحل نصب و راه‌اندازی گام به گام
- جدول کامل فیلدها (Meta vs Attribute)
- نحوه کار با تصاویر
- رفع مشکلات متداول
- کدهای نمونه
- لیست فایل‌های مهم

---

## 🔧 تغییرات تکنیکال

### قبل از این تغییرات:
```php
// Attribute های Local ساخته می‌شدند
$attribute->set_name('weight');  // Local attribute
$attribute->set_options(['10.5']);

// تصاویر با product_id=0 اضافه می‌شدند
media_handle_sideload($file, 0);

// تصاویر تکراری دانلود می‌شدند
// هیچ logging وجود نداشت
```

### بعد از این تغییرات:
```php
// Attribute های Global استفاده می‌شوند
if (taxonomy_exists('pa_weight')) {
    $term = wp_insert_term('10.5', 'pa_weight');
    wp_set_object_terms($product_id, $term_id, 'pa_weight');
    $attribute->set_id(wc_attribute_taxonomy_id_by_name('pa_weight'));
}

// تصاویر با product_id صحیح اضافه می‌شوند
if (!$product->get_id()) {
    $product->save();
}
media_handle_sideload($file, $product->get_id());

// بررسی تصاویر موجود
$existing = $this->findExistingAttachment(basename($imageUrl));

// Logging کامل
error_log('Zargar Image Success: Attached ID ' . $attachmentId);
```

---

## 📋 چک لیست تست

### ✅ قبل از ایمپورت:
- [ ] `setup-attributes.php` اجرا شده
- [ ] به `WooCommerce > ویژگی‌ها` رفته و 8 attribute وجود دارد
- [ ] به `تنظیمات > پیوندهای یکتا` رفته و ذخیره شده
- [ ] در `زرگر > تنظیمات` اطلاعات سرور تنظیم شده
- [ ] تست اتصال موفق است

### ✅ بعد از ایمپورت:
- [ ] محصول در `محصولات` ظاهر شده
- [ ] SKU صحیح است
- [ ] Meta fields در Meta Box "اطلاعات حسابداری زرگر" نمایش داده می‌شوند
- [ ] Attributes در بخش "ویژگی‌ها" نمایش داده می‌شوند
- [ ] تصویر شاخص نمایش داده می‌شود
- [ ] گالری تصاویر پر شده است
- [ ] قیمت‌ها صحیح هستند
- [ ] دسته‌بندی تنظیم شده

### ✅ بررسی Log:
```bash
tail -f /path/to/wordpress/wp-content/debug.log | grep "Zargar"
```

باید ببینید:
```
Zargar Image Success: Attached ID 123 | Product: 456 | URL: http://...
```

---

## 🚀 آماده استفاده!

همه چیز آماده است. فقط:

1. **اجرای یکباره:** `setup-attributes.php`
2. **تنظیم:** IP/Port/Username/Password
3. **ایمپورت:** دسته‌ای یا انتخابی

---

## 📞 پشتیبانی

اگر مشکلی پیش آمد:

1. فایل `debug.log` را بررسی کنید
2. `test-import-single.php` را اجرا کنید
3. خطاهای PHP را چک کنید
4. تست اتصال را دوباره انجام دهید

---

تاریخ: 2025-12-17  
نسخه: 2.0.0 Final  
وضعیت: ✅ تست شده و آماده
