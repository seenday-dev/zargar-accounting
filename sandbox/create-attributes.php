<?php
/**
 * Helper script to register WooCommerce product attributes without external files.
 *
 * Usage:
 *   wp eval-file sandbox/create-attributes.php
 */

declare(strict_types=1);

if (!defined('ABSPATH') || !class_exists('WooCommerce')) {
    exit('Run this inside WordPress with WooCommerce active.');
}

import_wc_attributes([
    ['label' => 'رنگ',  'slug' => 'colortitle'],
    ['label' => 'اجرت', 'slug' => 'wagepercent'],
    ['label' => 'وزن',  'slug' => 'weight'],
]);

function import_wc_attributes(array $attributes): void {
    $existingSlugs = array_map(static fn($tax) => $tax->attribute_name, wc_get_attribute_taxonomies());

    foreach ($attributes as $attr) {
        $label = $attr['label'] ?? '';
        $slug  = sanitize_title($attr['slug'] ?? '');

        if ($label === '' || $slug === '') {
            continue;
        }

        if (in_array($slug, $existingSlugs, true)) {
            printf("Skipped '%s' (slug '%s') – already exists.\n", $label, $slug);
            continue;
        }

        $result = wc_create_attribute([
            'slug'         => $slug,
            'name'         => $label,
            'type'         => 'select',
            'order_by'     => 'menu_order',
            'has_archives' => false,
        ]);

        if (is_wp_error($result)) {
            printf("Failed to create '%s': %s\n", $label, $result->get_error_message());
        } else {
            printf("Created attribute '%s'.\n", $label);
            $existingSlugs[] = $slug;
        }
    }
}
