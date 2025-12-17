# 🚨 راهنمای سریع: چرا attribute اجرت نمایش داده نمی‌شود؟

## مشکل
در صفحه ایمپورت، گزینه "اجرت" و "اجرت(درصد)" نمایش داده نمی‌شود.

## دلیل
مرورگر صفحه را cache کرده و JavaScript قدیمی را استفاده می‌کند.

## راه‌حل (3 گام)

### گام 1: پاک کردن Cache مرورگر ⭐
```
Chrome/Edge: Ctrl + Shift + Delete
Firefox: Ctrl + Shift + Delete
Safari: Cmd + Option + E
```

یا ساده‌تر:
```
Ctrl + F5 (Windows)
Cmd + Shift + R (Mac)
```

### گام 2: Hard Refresh ✅
```
در صفحه ایمپورت:
1. F12 را بزنید (DevTools باز شود)
2. روی دکمه Refresh کلیک راست کنید
3. "Empty Cache and Hard Reload" را بزنید
```

### گام 3: بررسی ✅
بعد از refresh صفحه، باید ببینید:

**در بخش "ویژگی‌ها":**
```
☑ مدل                  attribute:model
☑ رنگ                  attribute:color
☑ سایز                 attribute:size
☑ مجموعه               attribute:collection
☑ اجرت                 attribute:wage          ← جدید!
☑ اجرت(درصد)           attribute:wagepercent   ← جدید!
```

---

## اگر باز هم کار نکرد

### بررسی 1: آیا setup-attributes.php اجرا شده؟
```
http://localhost/wp/wp-content/plugins/zargar-accounting/setup-attributes.php
```

باید ببینید:
```
✓ Attribute 'اجرت' (pa_wage) با موفقیت ساخته شد
✓ Attribute 'اجرت(درصد)' (pa_wagepercent) از قبل وجود دارد
```

### بررسی 2: Console مرورگر
```
F12 > Console
```

باید چیزی شبیه این ببینید:
```javascript
{
  attributes: {
    title: "ویژگی‌ها",
    fields: {
      ModelTitle: {...},
      ColorTitle: {...},
      ...
      WageOfPrice: {label: "اجرت", target: "attribute:wage"},
      calculated_wage_percent: {label: "اجرت(درصد)", target: "attribute:wagepercent"}
    }
  }
}
```

### بررسی 3: API Response
در Console مرورگر این کد را اجرا کنید:
```javascript
jQuery.post(zargarAjax.ajaxurl, {
    action: 'zargar_get_import_stats',
    nonce: zargarAjax.importNonce
}, function(response) {
    console.log('Available Fields:', response.data.available_fields);
});
```

---

## نکته مهم: محاسبه خودکار

فیلد `calculated_wage_percent` در API وجود ندارد!
این فیلد **محاسباتی** است و در زمان ایمپورت محاسبه می‌شود:

```php
اجرت درصدی = (SaleWageOfPrice / TotalPrice) × 100
```

پس اگر این فیلد را انتخاب کنید، به طور خودکار محاسبه و به attribute `pa_wagepercent` اضافه می‌شود.

---

## خلاصه

1. ✅ فیلدها در FieldMapper.php اضافه شده‌اند
2. ✅ setup-attributes.php آماده است
3. ❌ مرورگر cache دارد → **Ctrl + F5**

**راه‌حل نهایی:**
```
Ctrl + F5 → صفحه را refresh کنید!
```

بعد از refresh، حتماً باید 9 ویژگی ببینید (نه 7).

---

تاریخ: 2025-12-17  
نسخه: 2.0.0
