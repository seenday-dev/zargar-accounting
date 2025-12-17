# 🔍 راهنمای debugging مشکل "خطا در ارتباط با سرور"

## ✅ چک‌لیست بررسی

### 1️⃣ بررسی پلاگین در WordPress

```bash
# در داشبورد WordPress:
افزونه‌ها → بررسی کن که "Zargar Accounting" فعال باشه
اگه غیرفعال بود → فعالش کن
اگه فعال بود → یک‌بار غیرفعال و دوباره فعال کن
```

### 2️⃣ بررسی خطاهای PHP

```bash
# فعال کردن WordPress Debug Mode
# در wp-config.php این خطوط رو اضافه کن (قبل از "That's all, stop editing!"):

define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
@ini_set('display_errors', 0);

# بعد از اضافه کردن، دوباره تست کن و فایل زیر رو چک کن:
tail -f /wp-content/debug.log
```

### 3️⃣ چک کردن AJAX Handler

```bash
# در Browser Console (F12):
fetch('/wp-admin/admin-ajax.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: 'action=zargar_search_products&codes=["GD01000316"]'
}).then(r => r.text()).then(console.log);
```

**نتایج معمول:**
- ✅ `{"success":false,"data":{"message":"...nonce..."}}` → Handler کار می‌کنه ولی nonce مشکل داره
- ✅ `{"success":false,"data":{"message":"دسترسی غیرمجاز"}}` → Handler کار می‌کنه ولی user دسترسی نداره
- ❌ `0` یا `-1` → Handler register نشده
- ❌ `404` → مسیر AJAX اشتباهه

### 4️⃣ بررسی Console Errors

```bash
# در Browser Console (F12) تب Console:
# وقتی روی "جستجو" می‌زنی، چه خطایی میاد؟

معمولا این خطاها رو می‌بینی:
- "Failed to fetch" → مشکل شبکه یا CORS
- "Unexpected token" → پاسخ JSON نیست (ممکنه HTML error برگردونه)
- "404 Not Found" → مسیر اشتباهه
- "500 Internal Server Error" → خطای PHP
```

### 5️⃣ بررسی Network Tab

```bash
# در Browser Console (F12) تب Network:
# فیلتر رو روی XHR بذار
# دوباره جستجو کن
# روی request کلیک کن

چک کن:
- Status Code: باید 200 باشه
- Response: چی برمی‌گردونه؟ JSON؟ HTML؟
- Request Payload: کدها درست فرستاده می‌شن؟
```

### 6️⃣ چک کردن Nonce

```bash
# در صفحه Import، کنسول رو باز کن و بنویس:
console.log(zargarAjax);

باید ببینی:
{
    ajaxurl: "/wp-admin/admin-ajax.php",
    importNonce: "abc123...",
    ...
}

اگه zargarAjax تعریف نشده بود، یعنی:
- پلاگین فعال نیست
- یا AssetsManager درست کار نمی‌کنه
- یا صفحه cache شده
```

### 7️⃣ تست مستقیم با cURL

```bash
# از ترمینال، nonce رو از کنسول بگیر و اینطوری تست کن:

curl -X POST 'http://your-site.com/wp-admin/admin-ajax.php' \
  -H 'Content-Type: application/x-www-form-urlencoded' \
  -d 'action=zargar_search_products' \
  -d 'nonce=YOUR_NONCE_HERE' \
  -d 'codes=["GD01000316","GD01000315"]' \
  --cookie 'wordpress_logged_in_xxx=YOUR_COOKIE'
```

### 8️⃣ بررسی لاگ‌های پلاگین

```bash
# لاگ‌های زرگر رو چک کن:
tail -f storage/logs/general/general-$(date +%Y-%m-%d).log

# اگه هیچ لاگی نمی‌افته یعنی:
- کد PHP اصلا اجرا نمیشه
- Handler register نشده
- Plugin غیرفعاله
```

### 9️⃣ Flush Cache

```bash
# اگه از cache plugin استفاده می‌کنی (مثل WP Super Cache):
- Cache رو پاک کن
- Browser cache رو هم پاک کن (Ctrl+Shift+Delete)
- دوباره تست کن
```

### 🔟 بررسی File Permissions

```bash
# مطمئن شو که فایل‌های PHP قابل خوندن هستن:
ls -la includes/Admin/ProductImportManager.php

# باید بتونی فایل رو بخونی:
php -l includes/Admin/ProductImportManager.php
# باید بگه: No syntax errors detected
```

---

## 🚀 راه‌حل‌های سریع

### مشکل: خطا در ارتباط با سرور

**احتمال 1: Plugin غیرفعال است**
```
حل: Dashboard → Plugins → Deactivate + Activate
```

**احتمال 2: AJAX Handler register نشده**
```php
// در zargar-accounting.php بعد از خط 56، اضافه کن:
error_log('ProductImportManager initialized: ' . print_r(class_exists('ZargarAccounting\Admin\ProductImportManager'), true));
```

**احتمال 3: Nonce مشکل داره**
```javascript
// موقتا nonce check رو comment کن برای تست:
// در ProductImportManager.php خط 439
// if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'zargar_import_nonce')) {
//     wp_send_json_error(['message' => 'خطای امنیتی: nonce نامعتبر است']);
//     return;
// }
```

**احتمال 4: Permission مشکل داره**
```javascript
// موقتا permission check رو comment کن:
// if (!current_user_can('manage_options')) {
//     wp_send_json_error(['message' => 'دسترسی غیرمجاز']);
//     return;
// }
```

**احتمال 5: CORS یا Server Config**
```bash
# در .htaccess اضافه کن:
Header set Access-Control-Allow-Origin "*"
```

---

## 📋 اطلاعات مورد نیاز برای debugging

وقتی مشکل رو گزارش می‌کنی، این اطلاعات رو بده:

1. **Browser Console Errors:**
   ```
   کامل خطاهایی که توی Console میاد رو copy کن
   ```

2. **Network Response:**
   ```
   Response تب رو توی Network باز کن و محتواش رو copy کن
   ```

3. **PHP Error Log:**
   ```
   tail -20 /wp-content/debug.log
   ```

4. **Plugin Status:**
   ```
   پلاگین فعاله؟ Version چنده؟
   ```

5. **WordPress Version:**
   ```
   Dashboard → About: نسخه وردپرس چنده؟
   ```

---

## 🎯 تست نهایی

بعد از هر تغییر:
1. پلاگین رو deactivate/activate کن
2. Browser cache رو پاک کن
3. صفحه Import رو Hard Refresh کن (Ctrl+Shift+R)
4. Console رو باز کن و خطاها رو ببین
5. دوباره تست کن
