<?php
declare(strict_types=1);

$fields = [
    'ProductId'          => 'meta:_external_id',
    'ProductCode'        => 'sku',
    'ProductTitle'       => 'post_title',
    'Weight'             => 'meta:_weight',
    'BaseWeight'         => 'meta:_base_weight',
    'IsExists'           => 'stock_status',
    'CategoryTitle'      => 'term:product_cat',
    'LocationTitle'      => 'meta:_location',
    'ImageURL1'          => 'gallery_image',
    'ImageURL2'          => 'gallery_image',
    'ImageURL3'          => 'gallery_image',
    'ImageURL4'          => 'gallery_image',
    'ImageURL5'          => 'gallery_image',
    'ImageURL6'          => 'gallery_image',
    'ModelTitle'         => 'attribute:model',
    'ColorTitle'         => 'attribute:pa_color',
    'SizeTitle'          => 'attribute:pa_size',
    'TaxPercent'         => 'tax_class',
    'WeightSymbolRate'   => 'meta:_weight_symbol_rate',
    'GoldPrice'          => 'regular_price',
    'StonePrice'         => 'meta:_stone_price',
    'WageOfPrice'        => 'meta:_wage_price',
    'IncomeTotal'        => 'meta:_income_total',
    'TaxTotal'           => 'meta:_tax_total',
    'TotalPrice'         => 'sale_price',
    'CollectionTitle'    => 'attribute:collection',
    'SaleWageOfPercent'  => 'meta:_sale_wage_percent',
    'SaleWageOfPrice'    => 'meta:_sale_wage_price',
    'SaleWageOfPriceType'=> 'meta:_sale_wage_price_type',
    'SaleStonePrice'     => 'meta:_sale_stone_price',
    'OldCode'            => 'meta:_old_code',
    'OfficeCode'         => 'meta:_office_code',
    'DesignerCode'       => 'meta:_designer_code',
    'other1'             => 'meta:_extra_field_1',
    'other2'             => 'meta:_extra_field_2',
    'DefaultImageURL'    => 'featured_image',
];

$options = [
    'none',
    'meta:_external_id',
    'sku',
    'post_title',
    'meta:_weight',
    'meta:_base_weight',
    'stock_status',
    'term:product_cat',
    'meta:_location',
    'gallery_image',
    'attribute:model',
    'attribute:pa_color',
    'attribute:pa_size',
    'tax_class',
    'meta:_weight_symbol_rate',
    'regular_price',
    'meta:_stone_price',
    'meta:_wage_price',
    'meta:_income_total',
    'meta:_tax_total',
    'sale_price',
    'attribute:collection',
    'meta:_sale_wage_percent',
    'meta:_sale_wage_price',
    'meta:_sale_wage_price_type',
    'meta:_sale_stone_price',
    'meta:_old_code',
    'meta:_office_code',
    'meta:_designer_code',
    'meta:_extra_field_1',
    'meta:_extra_field_2',
    'featured_image',
];
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>انتخاب نگاشت فیلدها</title>
    <style>
        body {
            font-family: sans-serif;
            background: #f8f9fa;
            padding: 30px;
            line-height: 1.6;
        }
        .mapping-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 12px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        .mapping-label {
            font-weight: 600;
            color: #333;
            min-width: 160px;
        }
        select {
            flex: 1;
            margin-left: 16px;
            padding: 8px 12px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <h1>انتخاب نگاشت فیلدها</h1>

    <?php foreach ($fields as $field => $selected): ?>
        <div class="mapping-row">
            <div class="mapping-label"><?= htmlspecialchars($field, ENT_QUOTES, 'UTF-8'); ?></div>
            <select>
                <?php foreach ($options as $option): ?>
                    <option value="<?= htmlspecialchars($option, ENT_QUOTES, 'UTF-8'); ?>"
                        <?= $option === $selected ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($option, ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    <?php endforeach; ?>
</body>
</html>
