<?php
declare(strict_types=1);

/**
 * Returns the WooCommerce target field for a given Zargar field.
 */
function map_zargar_field(string $field): ?string {
    static $map = null;

    if ($map === null) {
        $map = [
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
    }

    return $map[$field] ?? null;
}

/**
 * Example usage: convert an associative array to WooCommerce fields.
 */
function map_product_data(array $zargarProduct): array {
    $mapped = [];

    foreach ($zargarProduct as $key => $value) {
        $target = map_zargar_field((string) $key);
        if ($target === null) {
            continue;
        }

        $mapped[$target] = $value;
    }

    return $mapped;
}
