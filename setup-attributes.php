<?php
/**
 * ساخت Attribute های Global برای WooCommerce
 * این فایل تنها یکبار اجرا می‌شود تا attribute های مورد نیاز را بسازد
 */

// Load WordPress
define('WP_USE_THEMES', false);
require_once('../../../wp-load.php');

// Check if WooCommerce is active
if (!class_exists('WooCommerce')) {
    die('WooCommerce is not active!');
}

echo "<h1>ساخت Attribute های Global</h1>";
echo "<pre>";

// لیست attribute های مورد نیاز
$attributes = [
    'weight' => [
        'name' => 'وزن',
        'slug' => 'weight',
        'type' => 'text',
        'order_by' => 'menu_order'
    ],
    'base_weight' => [
        'name' => 'وزن پایه',
        'slug' => 'base_weight',
        'type' => 'text',
        'order_by' => 'menu_order'
    ],
    'wagepercent' => [
        'name' => 'اجرت(درصد)',
        'slug' => 'wagepercent',
        'type' => 'text',
        'order_by' => 'menu_order'
    ],
    'size' => [
        'name' => 'سایز',
        'slug' => 'size',
        'type' => 'select',
        'order_by' => 'menu_order'
    ],
    'model' => [
        'name' => 'مدل',
        'slug' => 'model',
        'type' => 'text',
        'order_by' => 'menu_order'
    ],
    'color' => [
        'name' => 'رنگ',
        'slug' => 'color',
        'type' => 'select',
        'order_by' => 'menu_order'
    ],
    'collection' => [
        'name' => 'مجموعه',
        'slug' => 'collection',
        'type' => 'select',
        'order_by' => 'menu_order'
    ],
    '_weight_symbol_rate' => [
        'name' => 'نرخ وزن',
        'slug' => '_weight_symbol_rate',
        'type' => 'text',
        'order_by' => 'menu_order'
    ]
];

echo "در حال ساخت " . count($attributes) . " attribute...\n\n";
echo str_repeat('=', 70) . "\n\n";

foreach ($attributes as $key => $attr) {
    $taxonomy_name = 'pa_' . $attr['slug'];
    
    // بررسی اینکه آیا قبلاً وجود دارد
    if (taxonomy_exists($taxonomy_name)) {
        echo "✓ Attribute '{$attr['name']}' ({$taxonomy_name}) از قبل وجود دارد\n";
        continue;
    }
    
    // ساخت attribute جدید
    $result = wc_create_attribute([
        'name' => $attr['name'],
        'slug' => $attr['slug'],
        'type' => $attr['type'],
        'order_by' => $attr['order_by'],
        'has_archives' => false,
    ]);
    
    if (is_wp_error($result)) {
        echo "✗ خطا در ساخت '{$attr['name']}': " . $result->get_error_message() . "\n";
    } else {
        // ثبت taxonomy
        register_taxonomy(
            $taxonomy_name,
            'product',
            [
                'labels' => [
                    'name' => $attr['name'],
                    'singular_name' => $attr['name'],
                ],
                'public' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'show_in_nav_menus' => true,
                'hierarchical' => false,
            ]
        );
        
        echo "✓ Attribute '{$attr['name']}' ({$taxonomy_name}) با موفقیت ساخته شد\n";
    }
}

echo "\n" . str_repeat('=', 70) . "\n";
echo "\n✅ تمام! اکنون attribute های زیر در WooCommerce موجود هستند:\n\n";

// نمایش لیست نهایی
$all_attributes = wc_get_attribute_taxonomies();
$our_slugs = array_column($attributes, 'slug');

foreach ($all_attributes as $attr) {
    if (in_array($attr->attribute_name, $our_slugs)) {
        echo "  • {$attr->attribute_label} (pa_{$attr->attribute_name})\n";
    }
}

echo "\n" . str_repeat('=', 70) . "\n";
echo "\nمی‌توانید به WooCommerce > محصولات > ویژگی‌ها بروید و آنها را مشاهده کنید.\n";
echo "\nهمچنین می‌توانید به WordPress > تنظیمات > Permalinks بروید و دکمه 'ذخیره تغییرات' را بزنید تا taxonomy ها به درستی ثبت شوند.\n";

echo "</pre>";

// Clear cache
flush_rewrite_rules();
delete_transient('wc_attribute_taxonomies');
