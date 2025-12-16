<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- LineIcons - Local Fixed -->
    <link rel="stylesheet" href="<?php echo ZARGAR_ACCOUNTING_PLUGIN_URL; ?>assets/icons/lineicons-fixed.css">
    
    <!-- Yekan Font -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/yekan-font@1.0.0/css/yekan-font.min.css">
    
    <style>
        * {
            font-family: 'yekan', 'Tahoma', 'Arial', sans-serif !important;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: #f8f9fa;
            color: #1a1a1a;
        }
        
        .wrap {
            max-width: 1400px;
            margin: 20px auto;
            padding: 0 20px;
        }
        
        h1 {
            font-size: 28px;
            margin-bottom: 30px;
            color: #000;
        }
        
        /* Tabs */
        .log-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .log-tab {
            flex: 1;
            padding: 15px 20px;
            background: #f8f9fa;
            border: 2px solid #e5e7eb;
            border-radius: 6px;
            text-decoration: none;
            color: #1a1a1a;
            font-size: 16px;
            font-weight: bold;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .log-tab:hover {
            background: #f3f4f6;
            border-color: #d1d5db;
            transform: translateY(-2px);
        }
        
        .log-tab.active {
            background: #000;
            color: white;
            border-color: #000;
        }
        
        .tab-badge {
            background: rgba(255,255,255,0.2);
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 12px;
            min-width: 20px;
            text-align: center;
        }
        
        .log-tab.active .tab-badge {
            background: rgba(255,255,255,0.3);
        }
        
        /* Table */
        .logs-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .logs-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .logs-table thead {
            background: #f8f9fa;
            border-bottom: 2px solid #e5e7eb;
        }
        
        .logs-table th {
            padding: 15px;
            text-align: right;
            font-weight: bold;
            color: #1a1a1a;
            font-size: 14px;
        }
        
        .logs-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #f3f4f6;
            font-size: 13px;
            color: #374151;
        }
        
        .logs-table tbody tr:hover {
            background: #f9fafb;
        }
        
        .log-time {
            color: #6b7280;
            font-size: 12px;
            white-space: nowrap;
        }
        
        .log-message {
            max-width: 500px;
            line-height: 1.5;
        }
        
        .empty-state {
            padding: 60px 20px;
            text-align: center;
            color: #6b7280;
        }
        
        .empty-state .icon {
            font-size: 48px;
            margin-bottom: 15px;
        }
        
        .empty-state h3 {
            font-size: 18px;
            margin-bottom: 8px;
            color: #1a1a1a;
        }
        
        /* Stats */
        .stats-summary {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            display: flex;
            gap: 20px;
            align-items: center;
        }
        
        .stat-item {
            flex: 1;
            text-align: center;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 6px;
        }
        
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #000;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 12px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="wrap">
        <h1>📊 گزارش‌های سیستم</h1>
        
        <?php
        // Variables are passed from LogsViewer::render()
        // $this, $current_channel, $valid_channels, $logs, $stats are already available
        $base_url = admin_url('admin.php?page=zargar-accounting-logs');
        ?>
        
        <!-- Stats Summary -->
        <div class="stats-summary">
            <div class="stat-item">
                <div class="stat-value"><?php echo $stats['product']['total']; ?></div>
                <div class="stat-label">📦 لاگ محصولات</div>
            </div>
            <div class="stat-item">
                <div class="stat-value"><?php echo $stats['sales']['total']; ?></div>
                <div class="stat-label">💰 لاگ فروش</div>
            </div>
            <div class="stat-item">
                <div class="stat-value"><?php echo $stats['price']['total']; ?></div>
                <div class="stat-label">💵 لاگ قیمت</div>
            </div>
            <div class="stat-item">
                <div class="stat-value"><?php echo $stats['error']['total']; ?></div>
                <div class="stat-label">⚠️ لاگ خطاها</div>
            </div>
        </div>
        
        <!-- Tabs -->
        <div class="log-tabs">
            <a href="<?php echo $base_url; ?>&channel=product" 
               class="log-tab <?php echo $current_channel === 'product' ? 'active' : ''; ?>">
                📦 محصولات
                <span class="tab-badge"><?php echo $stats['product']['total']; ?></span>
            </a>
            
            <a href="<?php echo $base_url; ?>&channel=sales" 
               class="log-tab <?php echo $current_channel === 'sales' ? 'active' : ''; ?>">
                💰 فروش
                <span class="tab-badge"><?php echo $stats['sales']['total']; ?></span>
            </a>
            
            <a href="<?php echo $base_url; ?>&channel=price" 
               class="log-tab <?php echo $current_channel === 'price' ? 'active' : ''; ?>">
                💵 قیمت
                <span class="tab-badge"><?php echo $stats['price']['total']; ?></span>
            </a>
            
            <a href="<?php echo $base_url; ?>&channel=error" 
               class="log-tab <?php echo $current_channel === 'error' ? 'active' : ''; ?>">
                ⚠️ خطاها
                <span class="tab-badge"><?php echo $stats['error']['total']; ?></span>
            </a>
        </div>
        
        <!-- Logs Table -->
        <div class="logs-container">
            <?php if (empty($logs)): ?>
                <div class="empty-state">
                    <div class="icon">📋</div>
                    <h3>لاگی موجود نیست</h3>
                    <p>هیچ لاگ <?php echo $valid_channels[$current_channel]; ?> ثبت نشده است.</p>
                </div>
            <?php else: ?>
                <table class="logs-table">
                    <thead>
                        <tr>
                            <th style="width: 150px;">زمان</th>
                            <th style="width: 100px;">سطح</th>
                            <th style="width: 100px;">کاربر</th>
                            <th>پیام</th>
                            <th style="width: 150px;">جزئیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td class="log-time"><?php echo esc_html($log['time']); ?></td>
                                <td><?php echo $this->formatLevel($log['level']); ?></td>
                                <td><?php echo esc_html($log['user']); ?></td>
                                <td class="log-message"><?php echo esc_html($log['message']); ?></td>
                                <td><?php echo $this->formatContext($log['context']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
