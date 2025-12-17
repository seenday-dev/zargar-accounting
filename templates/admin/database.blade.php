@include('components.header', ['title' => 'تنظیمات پیشرفته'])

<div class="zargar-content-wrapper">
    <aside class="zargar-sidebar-wrapper">
        @include('components.sidebar')
    </aside>
    
    <main class="zargar-main-content">
        <div class="content-inner">
            <h2 class="page-title" style="font-size: 28px; color: var(--gold-400); margin-bottom: var(--space-xl);">
                تنظیمات پیشرفته دیتابیس
            </h2>
            
            <!-- Database Status -->
            <div class="database-status-card">
                <div class="status-header">
                    <i class="lni lni-database"></i>
                    <h3>وضعیت دیتابیس</h3>
                </div>
                <div class="status-body">
                    <?php
                    $schema = \ZargarAccounting\Database\Schema::getInstance();
                    $repository = \ZargarAccounting\Database\ProductRepository::getInstance();
                    $table_exists = $schema->tablesExist();
                    $count = $table_exists ? $repository->count() : 0;
                    ?>
                    
                    <div class="status-item">
                        <span class="status-label">جدول زرگر:</span>
                        <span class="status-value <?php echo $table_exists ? 'success' : 'error'; ?>">
                            <?php echo $table_exists ? '✓ ایجاد شده' : '✗ ایجاد نشده'; ?>
                        </span>
                    </div>
                    
                    <?php if ($table_exists): ?>
                    <div class="status-item">
                        <span class="status-label">نام جدول:</span>
                        <span class="status-value code"><?php echo esc_html($schema->getTableName()); ?></span>
                    </div>
                    
                    <div class="status-item">
                        <span class="status-label">تعداد محصولات:</span>
                        <span class="status-value"><?php echo number_format($count); ?> محصول</span>
                    </div>
                    
                    <div class="status-item">
                        <span class="status-label">نسخه دیتابیس:</span>
                        <span class="status-value"><?php echo get_option('zargar_db_version', 'نامشخص'); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Actions Grid -->
            <div class="database-actions-grid">
                <!-- Create Tables Card -->
                <div class="action-card">
                    <div class="action-icon">
                        <i class="lni lni-plus"></i>
                    </div>
                    <h3>ایجاد جداول</h3>
                    <p>ایجاد جدول اختصاصی برای ذخیره‌سازی داده‌های زرگر</p>
                    <button class="btn btn-primary" id="create-tables" <?php echo $table_exists ? 'disabled' : ''; ?>>
                        <i class="lni lni-database"></i>
                        <?php echo $table_exists ? 'جداول موجود است' : 'ایجاد جداول'; ?>
                    </button>
                    <div class="action-result" id="create-result"></div>
                </div>
                
                <!-- Migrate Data Card -->
                <div class="action-card">
                    <div class="action-icon">
                        <i class="lni lni-reload"></i>
                    </div>
                    <h3>انتقال داده‌ها</h3>
                    <p>انتقال داده‌های موجود از postmeta به جدول اختصاصی</p>
                    <button class="btn btn-info" id="migrate-data" <?php echo !$table_exists ? 'disabled' : ''; ?>>
                        <i class="lni lni-upload"></i>
                        انتقال داده‌ها
                    </button>
                    <div class="action-result" id="migrate-result"></div>
                </div>
                
                <!-- Drop Tables Card -->
                <div class="action-card danger">
                    <div class="action-icon">
                        <i class="lni lni-trash"></i>
                    </div>
                    <h3>حذف جداول</h3>
                    <p class="warning-text">⚠️ تمام داده‌های زرگر حذف خواهند شد!</p>
                    <button class="btn btn-danger" id="drop-tables" <?php echo !$table_exists ? 'disabled' : ''; ?>>
                        <i class="lni lni-trash"></i>
                        حذف جداول
                    </button>
                    <div class="action-result" id="drop-result"></div>
                </div>
            </div>
            
            <!-- Info Box -->
            <div class="info-box">
                <div class="info-icon">
                    <i class="lni lni-information"></i>
                </div>
                <div class="info-content">
                    <h4>اطلاعات مهم</h4>
                    <ul>
                        <li>جدول اختصاصی عملکرد بسیار بهتری نسبت به postmeta دارد</li>
                        <li>برای محصولات بیش از 1000 عدد، استفاده از جدول اختصاصی ضروری است</li>
                        <li>پس از انتقال داده‌ها، اطلاعات از postmeta حذف نمی‌شوند (برای احتیاط)</li>
                        <li>می‌توانید هر زمان از postmeta به جدول اختصاصی migrate کنید</li>
                    </ul>
                </div>
            </div>
        </div>
    </main>
</div>

@include('components.footer')

<style>
.database-status-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    margin-bottom: 32px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.status-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 20px;
    padding-bottom: 16px;
    border-bottom: 2px solid #f0f0f0;
}

.status-header i {
    font-size: 32px;
    color: var(--gold-400);
}

.status-header h3 {
    font-size: 20px;
    margin: 0;
    color: var(--text-primary);
}

.status-body {
    display: grid;
    gap: 16px;
}

.status-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px;
    background: #f8f9fa;
    border-radius: 8px;
}

.status-label {
    font-weight: 600;
    color: var(--text-secondary);
}

.status-value {
    font-weight: 600;
    color: var(--text-primary);
}

.status-value.success {
    color: #28a745;
}

.status-value.error {
    color: #dc3545;
}

.status-value.code {
    font-family: 'Courier New', monospace;
    background: white;
    padding: 4px 10px;
    border-radius: 4px;
    font-size: 13px;
}

.database-actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 24px;
    margin-bottom: 32px;
}

.action-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    text-align: center;
    transition: transform 0.3s ease;
}

.action-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.12);
}

.action-card.danger {
    border: 2px solid #dc3545;
}

.action-icon {
    font-size: 48px;
    color: var(--gold-400);
    margin-bottom: 16px;
}

.action-card.danger .action-icon {
    color: #dc3545;
}

.action-card h3 {
    font-size: 20px;
    margin-bottom: 12px;
    color: var(--text-primary);
}

.action-card p {
    color: var(--text-secondary);
    margin-bottom: 20px;
    line-height: 1.6;
}

.warning-text {
    color: #dc3545 !important;
    font-weight: 600;
}

.action-result {
    margin-top: 16px;
}

.info-box {
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
    border-radius: 12px;
    padding: 24px;
    display: flex;
    gap: 20px;
}

.info-icon {
    font-size: 40px;
    color: #1976d2;
}

.info-content h4 {
    margin: 0 0 12px 0;
    color: #1976d2;
    font-size: 18px;
}

.info-content ul {
    margin: 0;
    padding-right: 20px;
    color: #0d47a1;
}

.info-content li {
    margin-bottom: 8px;
    line-height: 1.6;
}

.btn-danger {
    background: #dc3545;
    color: white;
}

.btn-danger:hover:not(:disabled) {
    background: #c82333;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Create Tables
    $('#create-tables').on('click', function() {
        if (!confirm('آیا مطمئن هستید که می‌خواهید جداول را ایجاد کنید؟')) return;
        
        const btn = $(this);
        const result = $('#create-result');
        
        btn.prop('disabled', true).html('<span class="loading-spinner"></span> در حال ایجاد...');
        result.html('');
        
        $.ajax({
            url: zargarAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'zargar_create_tables',
                nonce: zargarAjax.nonce
            },
            success: function(response) {
                if (response.success) {
                    result.html(`
                        <div class="import-success">
                            <i class="lni lni-checkmark-circle"></i>
                            ${response.data.message}
                        </div>
                    `);
                    setTimeout(() => location.reload(), 2000);
                } else {
                    result.html(`
                        <div class="import-error">
                            <i class="lni lni-close"></i>
                            ${response.data.message}
                        </div>
                    `);
                    btn.prop('disabled', false).html('<i class="lni lni-database"></i> ایجاد جداول');
                }
            }
        });
    });
    
    // Migrate Data
    $('#migrate-data').on('click', function() {
        if (!confirm('آیا مطمئن هستید که می‌خواهید داده‌ها را منتقل کنید؟')) return;
        
        const btn = $(this);
        const result = $('#migrate-result');
        
        btn.prop('disabled', true).html('<span class="loading-spinner"></span> در حال انتقال...');
        result.html('');
        
        $.ajax({
            url: zargarAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'zargar_migrate_data',
                nonce: zargarAjax.nonce
            },
            success: function(response) {
                if (response.success) {
                    result.html(`
                        <div class="import-success">
                            <i class="lni lni-checkmark-circle"></i>
                            ${response.data.message}
                        </div>
                    `);
                    setTimeout(() => location.reload(), 2000);
                } else {
                    result.html(`
                        <div class="import-error">
                            <i class="lni lni-close"></i>
                            ${response.data.message}
                        </div>
                    `);
                }
                btn.prop('disabled', false).html('<i class="lni lni-upload"></i> انتقال داده‌ها');
            }
        });
    });
    
    // Drop Tables
    $('#drop-tables').on('click', function() {
        if (!confirm('⚠️ هشدار: تمام داده‌های زرگر حذف خواهند شد!\n\nآیا مطمئن هستید؟')) return;
        if (!confirm('آخرین هشدار! این عمل قابل بازگشت نیست!')) return;
        
        const btn = $(this);
        const result = $('#drop-result');
        
        btn.prop('disabled', true).html('<span class="loading-spinner"></span> در حال حذف...');
        result.html('');
        
        $.ajax({
            url: zargarAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'zargar_drop_tables',
                nonce: zargarAjax.nonce
            },
            success: function(response) {
                if (response.success) {
                    result.html(`
                        <div class="import-success">
                            <i class="lni lni-checkmark-circle"></i>
                            ${response.data.message}
                        </div>
                    `);
                    setTimeout(() => location.reload(), 2000);
                } else {
                    result.html(`
                        <div class="import-error">
                            <i class="lni lni-close"></i>
                            ${response.data.message}
                        </div>
                    `);
                    btn.prop('disabled', false).html('<i class="lni lni-trash"></i> حذف جداول');
                }
            }
        });
    });
});
</script>
