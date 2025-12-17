<?php
declare(strict_types=1);

require_once __DIR__ . '/importproduct.php';

$sample = [
    'ProductId' => 3420,
    'ProductCode' => 'GD01000377',
    'ProductTitle' => 'انگشتر سولیتر',
];

$mapped = map_product_data($sample);
$fieldNames = [
    ['api' => 'ProductId', 'key' => 'meta:external_id', 'display' => 'متای شناسه خارجی (meta:external_id)', 'label' => 'شناسه خارجی'],
    ['api' => 'ProductCode', 'key' => 'sku', 'display' => 'کد محصول (sku)', 'label' => 'کد محصول'],
    ['api' => 'ProductTitle', 'key' => 'post_title', 'display' => 'عنوان محصول (post_title)', 'label' => 'عنوان محصول'],
    ['api' => 'Weight', 'key' => 'attribute:weight', 'display' => 'ویژگی وزن (attribute:weight)', 'label' => 'وزن'],
    ['api' => 'BaseWeight', 'key' => 'attribute:base_weight', 'display' => 'ویژگی وزن پایه (attribute:base_weight)', 'label' => 'وزن پایه'],
    ['api' => 'IsExists', 'key' => 'stock_status', 'display' => 'وضعیت موجودی (stock_status)', 'label' => 'وضعیت موجودی'],
    ['api' => 'CategoryTitle', 'key' => 'term:product_cat', 'display' => 'دسته‌بندی محصول (term:product_cat)', 'label' => 'دسته‌بندی'],
    ['api' => 'LocationTitle', 'key' => 'meta:location', 'display' => 'متای مکان محصول (meta:location)', 'label' => 'مکان'],
    ['api' => 'ImageURL1', 'key' => 'gallery_image', 'display' => 'تصویر گالری (gallery_image)', 'label' => 'تصویر گالری ۱'],
    ['api' => 'ImageURL2', 'key' => 'gallery_image', 'display' => 'تصویر گالری (gallery_image)', 'label' => 'تصویر گالری ۲'],
    ['api' => 'ImageURL3', 'key' => 'gallery_image', 'display' => 'تصویر گالری (gallery_image)', 'label' => 'تصویر گالری ۳'],
    ['api' => 'ImageURL4', 'key' => 'gallery_image', 'display' => 'تصویر گالری (gallery_image)', 'label' => 'تصویر گالری ۴'],
    ['api' => 'ImageURL5', 'key' => 'gallery_image', 'display' => 'تصویر گالری (gallery_image)', 'label' => 'تصویر گالری ۵'],
    ['api' => 'ImageURL6', 'key' => 'gallery_image', 'display' => 'تصویر گالری (gallery_image)', 'label' => 'تصویر گالری ۶'],
    ['api' => 'ModelTitle', 'key' => 'attribute:model', 'display' => 'ویژگی مدل (attribute:model)', 'label' => 'مدل'],
    ['api' => 'ColorTitle', 'key' => 'attribute:color', 'display' => 'ویژگی رنگ (attribute:color)', 'label' => 'رنگ'],
    ['api' => 'SizeTitle', 'key' => 'attribute:size', 'display' => 'ویژگی سایز (attribute:size)', 'label' => 'سایز'],
    ['api' => 'TaxPercent', 'key' => 'tax_class', 'display' => 'کلاس مالیاتی (tax_class)', 'label' => 'کلاس مالیاتی'],
    ['api' => 'WeightSymbolRate', 'key' => 'attribute:_weight_symbol_rate', 'display' => 'ویژگی نرخ وزن (attribute:_weight_symbol_rate)', 'label' => 'نرخ وزن'],
    ['api' => 'GoldPrice', 'key' => 'regular_price', 'display' => 'قیمت پایه محصول (regular_price)', 'label' => 'قیمت پایه'],
    ['api' => 'StonePrice', 'key' => 'meta:stone_price', 'display' => 'متای قیمت سنگ (meta:stone_price)', 'label' => 'قیمت سنگ'],
    ['api' => 'WageOfPrice', 'key' => 'meta:wage_price', 'display' => 'متای اجرت (meta:wage_price)', 'label' => 'اجرت'],
    ['api' => 'IncomeTotal', 'key' => 'meta:income_total', 'display' => 'متای مجموع درآمد (meta:income_total)', 'label' => 'مجموع درآمد'],
    ['api' => 'TaxTotal', 'key' => 'meta:tax_total', 'display' => 'متای مجموع مالیات (meta:tax_total)', 'label' => 'مجموع مالیات'],
    ['api' => 'TotalPrice', 'key' => 'sale_price', 'display' => 'قیمت فروش محصول (sale_price)', 'label' => 'قیمت فروش'],
    ['api' => 'CollectionTitle', 'key' => 'attribute:collection', 'display' => 'ویژگی مجموعه (attribute:collection)', 'label' => 'مجموعه'],
    ['api' => 'SaleWageOfPercent', 'key' => 'meta:sale_wage_percent', 'display' => 'متای درصد اجرت فروش (meta:sale_wage_percent)', 'label' => 'درصد اجرت فروش'],
    ['api' => 'SaleWageOfPrice', 'key' => 'meta:sale_wage_price', 'display' => 'متای مبلغ اجرت فروش (meta:sale_wage_price)', 'label' => 'مبلغ اجرت فروش'],
    ['api' => 'SaleWageOfPriceType', 'key' => 'meta:sale_wage_price_type', 'display' => 'متای نوع اجرت فروش (meta:sale_wage_price_type)', 'label' => 'نوع اجرت فروش'],
    ['api' => 'SaleStonePrice', 'key' => 'meta:sale_wage_stone', 'display' => 'متای اجرت سنگ فروش (meta:sale_wage_stone)', 'label' => 'اجرت سنگ فروش'],
    ['api' => 'OldCode', 'key' => 'meta:old_code', 'display' => 'متای کد قدیمی (meta:old_code)', 'label' => 'کد قدیمی'],
    ['api' => 'OfficeCode', 'key' => 'meta:office_code', 'display' => 'متای کد دفتر (meta:office_code)', 'label' => 'کد دفتر'],
    ['api' => 'DesignerCode', 'key' => 'meta:designer_code', 'display' => 'متای کد طراح (meta:designer_code)', 'label' => 'کد طراح'],
    ['api' => 'other1', 'key' => 'meta:extra_field_1', 'display' => 'متای فیلد اضافی ۱ (meta:extra_field_1)', 'label' => 'فیلد اضافی ۱'],
    ['api' => 'other2', 'key' => 'meta:extra_field_2', 'display' => 'متای فیلد اضافی ۲ (meta:extra_field_2)', 'label' => 'فیلد اضافی ۲'],
    ['api' => 'DefaultImageURL', 'key' => 'featured_image', 'display' => 'تصویر شاخص (featured_image)', 'label' => 'تصویر شاخص'],
];

$productCount = count($sample) > 0 ? 1 : 0;

?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>بررسی فیلدهای ورود محصول</title>
    <style>
        body {
            font-family: sans-serif;
            background: #f5f7fa;
            margin: 0;
        }
        .navbar {
            background: #1f2937;
            color: #fff;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .wrapper {
            max-width: 900px;
            margin: 30px auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(15,23,42,.1);
            padding: 25px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            border-bottom: 1px solid #eee;
            text-align: right;
        }
        th {
            background: #f1f5f9;
            font-weight: 600;
        }
        tr:hover td {
            background: #f9fafb;
        }
        .status {
            display: inline-flex;
            gap: 10px;
        }
        .status input {
            margin-left: 5px;
        }
        .import-btn {
            margin-top: 20px;
            padding: 12px 25px;
            background: #2563eb;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div>بررسی نقشه‌برداری محصول</div>
        <div>تعداد محصولات شناسایی شده: <?php echo $productCount; ?></div>
    </div>
    <div class="wrapper">
        <form>
            <table>
                <thead>
                    <tr>
                        <th>فیلد API</th>
                        <th>فیلد مقصد</th>
                        <th>عنوان فارسی</th>
                        <th>ایمپورت شود؟</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($fieldNames as $index => $field): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($field['api'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($field['display'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($field['label'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>
                            <div class="status">
                                <label>
                                    <input type="radio" name="field_<?php echo $index; ?>" value="yes">
                                    بله
                                </label>
                                <label>
                                    <input type="radio" name="field_<?php echo $index; ?>" value="no" checked>
                                    خیر
                                </label>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <button type="button" class="import-btn">ایمپورت</button>
        </form>
    </div>
</body>
</html>
