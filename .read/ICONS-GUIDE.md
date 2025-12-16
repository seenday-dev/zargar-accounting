# 🎨 راهنمای آیکون‌ها - Font Awesome 6

## تغییرات انجام شده

### قبل: ❌ Dashicons (WordPress)
- آیکون‌های WordPress که نمایش داده نمی‌شدند
- وابسته به فایل‌های WordPress
- محدود و قدیمی

### بعد: ✅ Font Awesome 6 (CDN)
- کتابخانه معتبر و مدرن
- بیش از 2000 آیکون رایگان
- CDN سریع و قابل اطمینان (Cloudflare)
- با فونت فارسی سازگار

---

## 📁 فایل‌های تغییر یافته

### 1. `templates/components/header.blade.php`

**اضافه شد:**
```html
<!-- Font Awesome 6 -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" 
      integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" 
      crossorigin="anonymous" 
      referrerpolicy="no-referrer" />
```

---

### 2. `templates/components/sidebar.blade.php`

**تغییرات:**

#### قبل:
```html
<span class="dashicons dashicons-dashboard"></span>
<span class="dashicons dashicons-admin-settings"></span>
<span class="dashicons dashicons-list-view"></span>
```

#### بعد:
```html
<i class="fas fa-home"></i>
<i class="fas fa-cog"></i>
<i class="fas fa-list-alt"></i>
```

---

### 3. `templates/admin/dashboard.blade.php`

**تغییرات:**

#### قبل:
```html
<span class="dashicons dashicons-chart-line"></span>
<span class="dashicons dashicons-yes-alt"></span>
<span class="dashicons dashicons-update"></span>
```

#### بعد:
```html
<i class="fas fa-chart-line"></i>
<i class="fas fa-check-circle"></i>
<i class="fas fa-sync-alt"></i>
```

---

### 4. `templates/admin/logs.blade.php`

**تغییرات:**

#### قبل:
```html
<span class="dashicons dashicons-update"></span>
<span class="dashicons dashicons-trash"></span>
```

#### بعد:
```html
<i class="fas fa-sync-alt"></i>
<i class="fas fa-trash"></i>
```

---

### 5. `assets/css/sidebar.css`

**تغییرات:**

#### قبل:
```css
.sidebar-menu-icon .dashicons {
    font-size: 16px;
    width: 16px;
    height: 16px;
}
```

#### بعد:
```css
.sidebar-menu-icon i {
    font-size: 16px;
    width: 16px;
    height: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
}
```

---

### 6. `assets/css/dashboard.css`

**تغییرات:**

#### قبل:
```css
.widget-icon .dashicons {
    font-size: 18px;
    color: var(--color-background);
}
```

#### بعد:
```css
.widget-icon i {
    font-size: 18px;
    color: var(--color-background);
}
```

---

## 🎯 آیکون‌های استفاده شده

| مکان | آیکون قدیم | آیکون جدید | Class |
|------|-----------|-----------|-------|
| سایدبار - داشبورد | dashicons-dashboard | 🏠 | `fas fa-home` |
| سایدبار - تنظیمات | dashicons-admin-settings | ⚙️ | `fas fa-cog` |
| سایدبار - گزارش‌ها | dashicons-list-view | 📋 | `fas fa-list-alt` |
| داشبورد - نمودار | dashicons-chart-line | 📈 | `fas fa-chart-line` |
| داشبورد - تیک | dashicons-yes-alt | ✅ | `fas fa-check-circle` |
| داشبورد - همگام‌سازی | dashicons-update | 🔄 | `fas fa-sync-alt` |
| لاگ‌ها - بروزرسانی | dashicons-update | 🔄 | `fas fa-sync-alt` |
| لاگ‌ها - حذف | dashicons-trash | 🗑️ | `fas fa-trash` |

---

## 🧪 تست آیکون‌ها

### تست خودکار:
```bash
# باز کردن فایل تست در مرورگر:
open test-fontawesome.html
# یا
firefox test-fontawesome.html
```

**چک‌لیست بصری**:
- [ ] همه آیکون‌ها نمایش داده می‌شوند
- [ ] فونت فارسی (ایران‌یکان) اعمال شده
- [ ] رنگ‌ها صحیح هستند
- [ ] سایز آیکون‌ها مناسب است

---

### تست در WordPress:

1. **آپلود فایل‌ها**:
   - `templates/components/header.blade.php`
   - `templates/components/sidebar.blade.php`
   - `templates/admin/dashboard.blade.php`
   - `templates/admin/logs.blade.php`
   - `assets/css/sidebar.css`
   - `assets/css/dashboard.css`

2. **پاک کردن کش**

3. **بازدید از داشبورد**:
   ```
   wp-admin/admin.php?page=zargar-accounting
   ```

4. **بررسی**:
   - [ ] آیکون‌های سایدبار نمایش داده می‌شوند
   - [ ] آیکون‌های ویجت‌های داشبورد نمایش داده می‌شوند
   - [ ] آیکون‌های صفحه لاگ‌ها نمایش داده می‌شوند

---

## 🔍 رفع مشکلات احتمالی

### مشکل 1: آیکون‌ها نمایش داده نمی‌شوند

**علت**: CDN در ایران فیلتر شده

**راه‌حل**:

#### الف) استفاده از VPN (موقت)
- فعال کردن VPN و تست مجدد

#### ب) استفاده از CDN دیگر:
```html
<!-- jsDelivr CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.1/css/all.min.css">
```

#### ج) دانلود و استفاده محلی:
```bash
# دانلود Font Awesome
cd assets/
mkdir fonts
cd fonts
wget https://use.fontawesome.com/releases/v6.5.1/fontawesome-free-6.5.1-web.zip
unzip fontawesome-free-6.5.1-web.zip
```

سپس در `header.blade.php`:
```html
<link rel="stylesheet" href="{{ $plugin_url }}assets/fonts/fontawesome-free-6.5.1-web/css/all.min.css">
```

---

### مشکل 2: آیکون‌ها مربع نشان داده می‌شوند

**علت**: Font بارگذاری نشده

**بررسی**:
```
F12 → Console
→ اگر خطای 404 دیدید، CDN مشکل دارد
```

**راه‌حل**: استفاده از CDN جایگزین (بالا)

---

### مشکل 3: آیکون‌ها خیلی بزرگ یا کوچک هستند

**راه‌حل**: تنظیم سایز در CSS
```css
/* برای سایدبار */
.sidebar-menu-icon i {
    font-size: 18px; /* به جای 16px */
}

/* برای داشبورد */
.widget-icon i {
    font-size: 24px; /* به جای 18px */
}
```

---

## 📚 منابع بیشتر

### Font Awesome Documentation:
- [Official Website](https://fontawesome.com)
- [Icons Gallery](https://fontawesome.com/icons)
- [Usage Guide](https://fontawesome.com/docs/web/setup/get-started)

### آیکون‌های پیشنهادی اضافی:

```html
<!-- محصولات -->
<i class="fas fa-box"></i>

<!-- فروش -->
<i class="fas fa-dollar-sign"></i>

<!-- قیمت -->
<i class="fas fa-tag"></i>

<!-- خطا -->
<i class="fas fa-exclamation-triangle"></i>

<!-- موفقیت -->
<i class="fas fa-check"></i>

<!-- اطلاعات -->
<i class="fas fa-info-circle"></i>

<!-- کاربر -->
<i class="fas fa-user"></i>

<!-- خروج -->
<i class="fas fa-sign-out-alt"></i>

<!-- کپی -->
<i class="fas fa-copy"></i>

<!-- ذخیره -->
<i class="fas fa-save"></i>
```

---

## ✅ چک‌لیست نهایی

### قبل از آپلود:
- [ ] فایل `test-fontawesome.html` را در مرورگر باز کردم
- [ ] همه آیکون‌ها نمایش داده می‌شوند
- [ ] فونت فارسی صحیح است

### بعد از آپلود:
- [ ] آیکون‌های سایدبار کار می‌کنند
- [ ] آیکون‌های داشبورد کار می‌کنند
- [ ] آیکون‌های صفحه لاگ‌ها کار می‌کنند
- [ ] هیچ خطایی در Console نیست

---

## 🎉 نتیجه

همه آیکون‌های پلاگین از **Dashicons** به **Font Awesome 6** تغییر کرد:

✅ **معتبرتر**: کتابخانه استاندارد جهانی
✅ **سریع‌تر**: CDN Cloudflare
✅ **زیباتر**: آیکون‌های مدرن و واضح
✅ **سازگار**: با فونت فارسی مشکلی ندارد
✅ **رایگان**: نسخه Free کافی است

---

**نسخه**: 2.1.0
**تاریخ**: ۱۴۰۳/۰۹/۲۵
**وضعیت**: ✅ آماده تست
