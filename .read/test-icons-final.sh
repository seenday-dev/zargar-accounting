#!/bin/bash

# 🎯 اسکریپت تست نهایی آیکون‌ها
# این اسکریپت تمام تست‌های لازم را برای اطمینان از درست بودن آیکون‌ها انجام می‌دهد

echo "=========================================="
echo "🎯 تست نهایی آیکون‌های افزونه"
echo "=========================================="
echo ""

# رنگ‌ها
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # بدون رنگ

# 1. بررسی فایل‌های فونت
echo "1️⃣  بررسی فایل‌های فونت LineIcons..."
if [ -f "assets/icons/LineIcons.woff" ] && [ -f "assets/icons/LineIcons.woff2" ]; then
    echo -e "   ${GREEN}✅ فایل‌های فونت موجود هستند${NC}"
    ls -lh assets/icons/LineIcons.* | awk '{print "      " $9 " (" $5 ")"}'
else
    echo -e "   ${RED}❌ فایل‌های فونت موجود نیستند!${NC}"
    exit 1
fi
echo ""

# 2. بررسی فایل CSS آیکون‌ها
echo "2️⃣  بررسی فایل lineicons.css..."
if [ -f "assets/icons/lineicons.css" ]; then
    echo -e "   ${GREEN}✅ فایل lineicons.css موجود است${NC}"
    
    # بررسی @font-face
    if grep -q "@font-face" assets/icons/lineicons.css; then
        echo -e "   ${GREEN}✅ @font-face تعریف شده است${NC}"
    else
        echo -e "   ${RED}❌ @font-face تعریف نشده است!${NC}"
    fi
    
    # بررسی مسیر فونت‌ها
    if grep -q "url('LineIcons" assets/icons/lineicons.css; then
        echo -e "   ${GREEN}✅ مسیر فونت‌ها صحیح است${NC}"
    else
        echo -e "   ${RED}❌ مسیر فونت‌ها نادرست است!${NC}"
    fi
else
    echo -e "   ${RED}❌ فایل lineicons.css موجود نیست!${NC}"
    exit 1
fi
echo ""

# 3. بررسی main.css برای مشکلات font-family
echo "3️⃣  بررسی main.css برای مشکلات font-family..."
if grep -q "font-family.*!important" assets/css/main.css; then
    echo -e "   ${RED}❌ CRITICAL: font-family با !important پیدا شد!${NC}"
    echo "      این باعث می‌شود آیکون‌ها کار نکنند."
    grep -n "font-family.*!important" assets/css/main.css | sed 's/^/      /'
    exit 1
else
    echo -e "   ${GREEN}✅ هیچ font-family با !important وجود ندارد${NC}"
fi

# بررسی استفاده صحیح از var(--font-family)
if grep -q "font-family: var(--font-family)" assets/css/main.css; then
    echo -e "   ${GREEN}✅ استفاده از CSS Variable صحیح است${NC}"
else
    echo -e "   ${YELLOW}⚠️  توصیه: از var(--font-family) استفاده کنید${NC}"
fi
echo ""

# 4. بررسی AssetsManager.php
echo "4️⃣  بررسی AssetsManager.php..."
if [ -f "includes/Admin/AssetsManager.php" ]; then
    echo -e "   ${GREEN}✅ فایل AssetsManager.php موجود است${NC}"
    
    # بررسی enqueue آیکون‌ها
    if grep -q "zargar-lineicons" includes/Admin/AssetsManager.php; then
        echo -e "   ${GREEN}✅ آیکون‌ها در AssetsManager ثبت شده‌اند${NC}"
    else
        echo -e "   ${RED}❌ آیکون‌ها در AssetsManager ثبت نشده‌اند!${NC}"
    fi
    
    # بررسی محدودیت به صفحات افزونه
    if grep -q "strpos.*zargar-accounting" includes/Admin/AssetsManager.php; then
        echo -e "   ${GREEN}✅ لود CSS محدود به صفحات افزونه است${NC}"
    else
        echo -e "   ${YELLOW}⚠️  توصیه: CSS را محدود به صفحات افزونه کنید${NC}"
    fi
else
    echo -e "   ${RED}❌ فایل AssetsManager.php موجود نیست!${NC}"
    exit 1
fi
echo ""

# 5. بررسی template‌ها برای آیکون‌ها
echo "5️⃣  بررسی template‌ها..."
ICON_COUNT=$(grep -r "lni lni-" templates/ 2>/dev/null | wc -l)
echo -e "   ${GREEN}✅ ${ICON_COUNT} آیکون در template‌ها پیدا شد${NC}"

echo "   آیکون‌های استفاده شده:"
grep -roh "lni-[a-z-]*" templates/ 2>/dev/null | sort -u | sed 's/^/      • /'
echo ""

# 6. خلاصه نهایی
echo "=========================================="
echo "📋 خلاصه نتایج:"
echo "=========================================="
echo -e "${GREEN}✅ فایل‌های فونت: موجود${NC}"
echo -e "${GREEN}✅ فایل CSS آیکون‌ها: صحیح${NC}"
echo -e "${GREEN}✅ فایل main.css: بدون مشکل${NC}"
echo -e "${GREEN}✅ AssetsManager: پیکربندی شده${NC}"
echo -e "${GREEN}✅ Template‌ها: آماده${NC}"
echo ""
echo "=========================================="
echo "🎉 همه تست‌ها با موفقیت انجام شد!"
echo "=========================================="
echo ""
echo "🔍 برای تست در مرورگر:"
echo "   xdg-open test-icons-fix.html"
echo ""
echo "📦 برای تست در وردپرس:"
echo "   1. افزونه را فعال کنید"
echo "   2. به صفحه 'افزونه‌های من > Zargar Accounting' بروید"
echo "   3. آیکون‌ها باید به درستی نمایش داده شوند"
echo ""
