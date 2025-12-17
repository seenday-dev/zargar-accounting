<?php
/**
 * تست مستقیم AJAX handler
 */

// شبیه‌سازی محیط WordPress
define('ABSPATH', __DIR__ . '/../');
$_POST['codes'] = '["GD01000316","GD01000315"]';
$_POST['action'] = 'zargar_search_products';
$_POST['nonce'] = 'test';

// Parse codes
$codes = isset($_POST['codes']) ? json_decode(stripslashes($_POST['codes']), true) : [];

echo "📦 داده دریافتی:\n";
echo "   Raw: " . $_POST['codes'] . "\n";
echo "   After stripslashes: " . stripslashes($_POST['codes']) . "\n";
echo "   After json_decode: " . print_r($codes, true) . "\n";
echo "   Empty? " . (empty($codes) ? 'YES ❌' : 'NO ✅') . "\n";
echo "\n";

// تست بدون stripslashes
$codes2 = isset($_POST['codes']) ? json_decode($_POST['codes'], true) : [];
echo "🧪 بدون stripslashes:\n";
echo "   After json_decode: " . print_r($codes2, true) . "\n";
echo "   Empty? " . (empty($codes2) ? 'YES ❌' : 'NO ✅') . "\n";
