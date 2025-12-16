# 🚀 راهنمای آپلود فایل‌ها به سرور

## 📦 فایل‌های آماده برای آپلود

### در Local (آماده):
```
/home/morpheus/Documents/zargar-accounting/
├── assets/icons/
│   ├── lineicons.css           (2 KB) ← نسخه جدید
│   ├── LineIcons.woff2         (71 KB)
│   └── LineIcons.woff          (87 KB)
└── includes/Admin/
    └── AssetsManager.php       (به‌روز شده)
```

### در Server (باید آپلود شوند):
```
/wp-content/plugins/zargar-accounting/
├── assets/icons/
│   ├── lineicons.css           ← جایگزین کن
│   ├── LineIcons.woff2         ← بررسی کن موجود باشه
│   └── LineIcons.woff          ← بررسی کن موجود باشه
└── includes/Admin/
    └── AssetsManager.php       ← جایگزین کن
```

---

## 🔧 روش 1: آپلود از طریق FTP/SFTP

### گام 1: اتصال به سرور
```
Host: sand.arista.gold
Port: 21 (FTP) یا 22 (SFTP)
Username: [نام کاربری FTP شما]
Password: [رمز عبور FTP]
```

### گام 2: رفتن به مسیر افزونه
```
cd /wp-content/plugins/zargar-accounting/
```

### گام 3: آپلود فایل‌ها
**الف) فایل‌های آیکون:**
```bash
# رفتن به پوشه آیکون‌ها
cd assets/icons/

# آپلود فایل‌ها (با FileZilla یا WinSCP)
1. lineicons.css (2KB) - جایگزین کن
2. LineIcons.woff2 (71KB) - اگر نیست آپلود کن
3. LineIcons.woff (87KB) - اگر نیست آپلود کن
```

**ب) فایل AssetsManager:**
```bash
cd includes/Admin/
# آپلود AssetsManager.php
```

### گام 4: بررسی مجوزها
```bash
chmod 644 assets/icons/lineicons.css
chmod 644 assets/icons/LineIcons.woff2
chmod 644 assets/icons/LineIcons.woff
chmod 644 includes/Admin/AssetsManager.php
```

---

## 🔧 روش 2: آپلود از طریق cPanel File Manager

### گام 1: ورود به cPanel
1. برو به: `https://sand.arista.gold:2083`
2. وارد شو با Username/Password

### گام 2: باز کردن File Manager
1. در cPanel روی **File Manager** کلیک کن
2. برو به: `public_html/wp-content/plugins/zargar-accounting/`

### گام 3: آپلود فایل آیکون‌ها
1. برو به پوشه: `assets/icons/`
2. روی **Upload** کلیک کن
3. فایل‌های زیر را انتخاب کن:
   - `/home/morpheus/Documents/zargar-accounting/assets/icons/lineicons.css`
   - `/home/morpheus/Documents/zargar-accounting/assets/icons/LineIcons.woff2`
   - `/home/morpheus/Documents/zargar-accounting/assets/icons/LineIcons.woff`
4. اگر `lineicons.css` قبلی وجود داشت، **Replace** کن

### گام 4: آپلود AssetsManager
1. برگرد به: `zargar-accounting/includes/Admin/`
2. فایل `AssetsManager.php` را آپلود کن
3. اگر قبلی بود **Replace** کن

---

## 🔧 روش 3: آپلود از طریق SSH

اگر به SSH دسترسی داری:

```bash
# اتصال به سرور
ssh user@sand.arista.gold

# رفتن به مسیر افزونه
cd /home/[username]/public_html/wp-content/plugins/zargar-accounting/

# آپلود فایل‌ها از local (از کامپیوتر خودت اجرا کن)
scp /home/morpheus/Documents/zargar-accounting/assets/icons/lineicons.css user@sand.arista.gold:/home/[username]/public_html/wp-content/plugins/zargar-accounting/assets/icons/

scp /home/morpheus/Documents/zargar-accounting/assets/icons/LineIcons.woff2 user@sand.arista.gold:/home/[username]/public_html/wp-content/plugins/zargar-accounting/assets/icons/

scp /home/morpheus/Documents/zargar-accounting/assets/icons/LineIcons.woff user@sand.arista.gold:/home/[username]/public_html/wp-content/plugins/zargar-accounting/assets/icons/

scp /home/morpheus/Documents/zargar-accounting/includes/Admin/AssetsManager.php user@sand.arista.gold:/home/[username]/public_html/wp-content/plugins/zargar-accounting/includes/Admin/

# تنظیم مجوزها
chmod 644 assets/icons/*
chmod 644 includes/Admin/AssetsManager.php
```

---

## 🔧 روش 4: آپلود از طریق WP Admin (اگر امکان داره)

اگر افزونه‌ای برای ویرایش فایل داری (مثل File Manager):

1. برو به: **WP Admin → Plugins → File Manager**
2. مسیر: `wp-content/plugins/zargar-accounting/assets/icons/`
3. فایل‌ها رو آپلود کن

---

## ✅ بعد از آپلود - چک‌لیست

### 1. بررسی فایل‌ها در سرور
این URLها باید کار کنند:
```
https://sand.arista.gold/wp-content/plugins/zargar-accounting/assets/icons/lineicons.css
https://sand.arista.gold/wp-content/plugins/zargar-accounting/assets/icons/LineIcons.woff2
https://sand.arista.gold/wp-content/plugins/zargar-accounting/assets/icons/LineIcons.woff
```

### 2. پاک کردن Cache
```bash
# از طریق WP-CLI (اگر داری)
wp cache flush

# یا از داشبورد وردپرس
WP Admin → Plugins → [Cache Plugin] → Clear All Cache
```

### 3. پاک کردن Cache مرورگر
```
Chrome/Firefox: Ctrl + Shift + R
یا
Ctrl + F5
```

### 4. غیرفعال/فعال کردن افزونه
```
WP Admin → Plugins → Zargar Accounting
1. Deactivate
2. Activate
```

### 5. تست نهایی
برو به:
```
https://sand.arista.gold/wp-admin/admin.php?page=zargar-accounting
```

باید آیکون‌ها رو ببینی! ✨

---

## 🐛 اگر هنوز کار نکرد

### چک کن:
1. **مسیر فایل‌ها درست است؟**
   ```
   /wp-content/plugins/zargar-accounting/assets/icons/lineicons.css
   ```

2. **مجوزها درست است؟**
   ```bash
   ls -la assets/icons/
   # باید 644 یا 755 باشند
   ```

3. **فایل CSS جدید است؟**
   باز کن و ببین اولین خطش:
   ```css
   /* Lineicons - Local Version */
   /* Fixed for local usage - Only WOFF/WOFF2 */
   ```
   
   اگر این رو نداره، یعنی فایل قدیمی هنوز اونجاست!

4. **Console مرورگر چی میگه؟**
   ```
   F12 → Console Tab
   F12 → Network Tab → فیلتر CSS/Font
   ```

5. **فایل‌های فونت دانلود می‌شن؟**
   ```
   F12 → Network → فیلتر Font
   # باید LineIcons.woff2 رو با status 200 ببینی
   ```

---

## 📝 نکات مهم

1. ⚠️ **حتماً فایل قدیمی رو Backup بگیر**
2. ⚠️ **بعد از آپلود Cache رو پاک کن**
3. ⚠️ **اگر CDN داری (مثل Cloudflare) حتماً Purge کن**
4. ⚠️ **مجوزهای فایل‌ها رو چک کن (644)**

---

## 🎉 بعد از موفقیت

وقتی همه چیز کار کرد:
- ✅ آیکون‌ها در Dashboard نمایش داده می‌شوند
- ✅ هیچ خطای 404 در Console نیست
- ✅ فونت‌ها با موفقیت لود شده‌اند
- ✅ عملکرد سریع و روان است

یک اسکرین‌شات بگیر و نگه دار! 📸

---

**آخرین به‌روزرسانی:** ۲۶ آذر ۱۴۰۴
