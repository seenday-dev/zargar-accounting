<?php
declare(strict_types=1);

require_once __DIR__ . '/importproduct.php';

// نمونه داده محصول زرگر (می‌توانی این بخش را به دلخواه تغییر دهی یا از API بخوانی)
$sampleProduct = [
    'ProductId' => 3420,
    'ProductCode' => 'GD01000377',
    'ProductTitle' => 'انگشتر سولیتر',
    'Weight' => 4.1300,
    'BaseWeight' => 4.1300,
    'IsExists' => true,
    'CategoryTitle' => 'کارساخته بازار',
    'LocationTitle' => '{ویترین بندی نشده}',
    'ImageURL1' => '/files/Img20251213-121.jpg',
    'ImageURL2' => '',
    'ImageURL3' => '',
    'ImageURL4' => '',
    'ImageURL5' => '',
    'ImageURL6' => '',
    'ModelTitle' => 'انگشتر',
    'ColorTitle' => 'دورنگ',
    'SizeTitle' => '',
    'TaxPercent' => 10.0000,
    'WeightSymbolRate' => 134450000.0000,
    'GoldPrice' => 555278500.0000,
    'StonePrice' => 0.0000,
    'WageOfPrice' => 74962598.0000,
    'IncomeTotal' => 44116877.0000,
    'TaxTotal' => 11907947.0000,
    'TotalPrice' => 686265922.0000,
    'CollectionTitle' => '',
    'SaleWageOfPercent' => 0.5576,
    'SaleWageOfPrice' => 0.0000,
    'SaleWageOfPriceType' => 2,
    'SaleStonePrice' => 0.0000,
    'OldCode' => '',
    'OfficeCode' => '',
    'DesignerCode' => '',
    'other1' => '',
    'other2' => '',
    'DefaultImageURL' => '/files/Img20251213-121.jpg',
];

$mapped = map_product_data($sampleProduct);

?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تست Import محصولات</title>
    <style>
        body {
            font-family: sans-serif;
            background: #f8f9fa;
            padding: 30px;
            line-height: 1.6;
        }
        .wrapper {
            max-width: 900px;
            margin: 0 auto;
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }
        h1 {
            margin-top: 0;
            color: #333;
        }
        pre {
            background: #272822;
            color: #f8f8f2;
            padding: 15px;
            border-radius: 8px;
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background: #f1f1f1;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <h1>تست نگاشت و Import محصولات</h1>

        <h2>داده خام</h2>
        <pre><?php echo htmlspecialchars(json_encode($sampleProduct, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8'); ?></pre>

        <h2>نتیجه نگاشت به WooCommerce</h2>
        <pre><?php echo htmlspecialchars(json_encode($mapped, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8'); ?></pre>

        <h2>نمایش جدولی</h2>
        <table>
            <thead>
                <tr>
                    <th>فیلد مقصد</th>
                    <th>مقدار</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($mapped as $target => $value): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($target, ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo is_scalar($value)
                            ? htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8')
                            : htmlspecialchars(json_encode($value, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
