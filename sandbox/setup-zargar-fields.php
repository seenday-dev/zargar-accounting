<?php
/**
 * Helper script to create Zargar Accounting meta fields and attributes in WooCommerce.
 *
 * Usage:
 *   wp eval-file sandbox/setup-zargar-fields.php
 */
declare(strict_types=1);

if (!defined('ABSPATH') || !class_exists('WooCommerce')) {
    exit('Run this inside WordPress with WooCommerce active.');
}

add_action('admin_notices', static function () {
    printf('<div class="notice notice-success"><p>%s</p></div>', esc_html__('متادیتاهای حسابداری زرگر و ویژگی‌ها ثبت شدند.', 'zargar-setup'));
});

register_zargar_meta_fields();
register_zargar_attributes();

function register_zargar_meta_fields(): void {
    $metaFields = [
        ['meta_key' => 'location', 'label' => 'محل نگهداری'],
        ['meta_key' => 'external_id', 'label' => 'شناسه خارجی'],
        ['meta_key' => 'stone_price', 'label' => 'قیمت سنگ'],
        ['meta_key' => 'wage_price', 'label' => 'قیمت اجرت'],
        ['meta_key' => 'income_total', 'label' => 'جمع درآمد'],
        ['meta_key' => 'tax_total', 'label' => 'جمع مالیات'],
        ['meta_key' => 'sale_wage_percent', 'label' => 'درصد اجرت فروش'],
        ['meta_key' => 'sale_wage_price', 'label' => 'مبلغ اجرت فروش'],
        ['meta_key' => 'sale_wage_price_type', 'label' => 'نوع اجرت فروش'],
        ['meta_key' => 'sale_wage_stone', 'label' => 'اجرت سنگ فروش'],
        ['meta_key' => 'office_code', 'label' => 'کد دفتر'],
        ['meta_key' => 'designer_code', 'label' => 'کد طراح'],
        ['meta_key' => 'extra_field_1', 'label' => 'فیلد اضافه ۱'],
        ['meta_key' => 'extra_field_2', 'label' => 'فیلد اضافه ۲'],
    ];

    echo "\n=== متادیتاهای حسابداری زرگر ===\n";
    foreach ($metaFields as $meta) {
        register_post_meta('product', $meta['meta_key'], [
            'type' => 'string',
            'description' => $meta['label'],
            'single' => true,
            'show_in_rest' => true,
        ]);
        printf("Registered meta '%s' (%s)\n", $meta['meta_key'], $meta['label']);
    }
}

function register_zargar_attributes(): void {
    $attributes = [
        ['slug' => 'base_weight', 'label' => 'وزن پایه'],
        ['slug' => 'weight', 'label' => 'وزن'],
        ['slug' => 'model', 'label' => 'مدل'],
        ['slug' => 'collection', 'label' => 'مجموعه'],
        ['slug' => 'color', 'label' => 'رنگ'],
        ['slug' => 'size', 'label' => 'سایز'],
        ['slug' => '_weight_symbol_rate', 'label' => 'نرخ وزن'],
    ];

    echo "\n=== ویژگی‌های محصول ===\n";
    $existing = wc_get_attribute_taxonomies();
    $existingSlugs = array_map(static fn($tax) => $tax->attribute_name, $existing);

    foreach ($attributes as $attr) {
        if (in_array($attr['slug'], $existingSlugs, true)) {
            printf("Attribute '%s' (%s) already exists.\n", $attr['slug'], $attr['label']);
            continue;
        }

        $result = wc_create_attribute([
            'slug'         => $attr['slug'],
            'name'         => $attr['label'],
            'type'         => 'select',
            'order_by'     => 'menu_order',
            'has_archives' => false,
        ]);

        if (is_wp_error($result)) {
            printf("Failed to create attribute '%s': %s\n", $attr['label'], $result->get_error_message());
        } else {
            printf("Created attribute '%s' (%s).\n", $attr['slug'], $attr['label']);
        }
    }
}
