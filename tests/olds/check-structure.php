<?php
/**
 * Check Log Structure
 */

header('Content-Type: application/json');

$base_dir = __DIR__ . '/storage/logs';

$channels = ['product', 'sales', 'price', 'error', 'general'];
$result = [
    'success' => true,
    'base_dir' => $base_dir,
    'channels' => []
];

foreach ($channels as $channel) {
    $channel_dir = $base_dir . '/' . $channel;
    $exists = is_dir($channel_dir);
    
    $logs = [];
    if ($exists) {
        $files = glob($channel_dir . '/*.log');
        foreach ($files as $file) {
            $logs[] = [
                'file' => basename($file),
                'size' => filesize($file),
                'lines' => count(file($file)),
                'modified' => date('Y-m-d H:i:s', filemtime($file))
            ];
        }
    }
    
    $result['channels'][$channel] = [
        'exists' => $exists,
        'path' => $channel_dir,
        'files' => $logs
    ];
}

echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
