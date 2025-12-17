@include('components.header', ['title' => 'ایمپورت'])

<div class="zargar-content-wrapper">
    <aside class="zargar-sidebar-wrapper">
        @include('components.sidebar')
    </aside>
    
    <main class="zargar-main-content">
        <div class="content-inner">
            <h2 class="page-title" style="font-size: 28px; color: var(--gold-400); margin-bottom: var(--space-xl);">ایمپورت داده‌ها از سیستم زرگر</h2>
            
            <!-- Import Cards -->
            <div class="import-grid">
                <!-- Metadata Card -->
                <div class="import-card">
                    <div class="import-card-icon">
                        <i class="lni lni-database"></i>
                    </div>
                    <h3 class="import-card-title">متادیتای محصولات</h3>
                    <p class="import-card-description">
                        ثبت ۱۴ فیلد اضافی محصولات شامل قیمت سنگ، اجرت، کد دفتر، کد طراح و سایر فیلدهای حسابداری
                    </p>
                    <button class="btn btn-primary btn-import" data-import-type="metadata">
                        <i class="lni lni-download"></i>
                        ایمپورت متادیتا
                    </button>
                    <div class="import-result" id="metadata-result"></div>
                </div>
                
                <!-- Attributes Card -->
                <div class="import-card">
                    <div class="import-card-icon">
                        <i class="lni lni-tag"></i>
                    </div>
                    <h3 class="import-card-title">ویژگی‌های محصولات</h3>
                    <p class="import-card-description">
                        ایجاد ۷ ویژگی WooCommerce شامل وزن، وزن پایه، مدل، مجموعه، رنگ، سایز و نرخ وزن
                    </p>
                    <button class="btn btn-primary btn-import" data-import-type="attributes">
                        <i class="lni lni-download"></i>
                        ایمپورت ویژگی‌ها
                    </button>
                    <div class="import-result" id="attributes-result"></div>
                </div>
                
                <!-- Products Card (Coming Soon) -->
                <div class="import-card import-card-disabled">
                    <div class="import-card-icon">
                        <i class="lni lni-cart"></i>
                    </div>
                    <h3 class="import-card-title">محصولات</h3>
                    <p class="import-card-description">
                        همگام‌سازی و ایمپورت محصولات از سرور حسابداری زرگر به ووکامرس
                    </p>
                    <button class="btn btn-secondary" disabled>
                        <i class="lni lni-hourglass"></i>
                        به‌زودی...
                    </button>
                </div>
            </div>
            
            <!-- Import Log -->
            <div class="import-log">
                <h3><i class="lni lni-list"></i> لاگ عملیات</h3>
                <div class="import-log-content" id="import-log">
                    <p class="import-log-empty">هنوز عملیات ایمپورتی انجام نشده است</p>
                </div>
            </div>
        </div>
    </main>
</div>

@include('components.footer')

<script>
jQuery(document).ready(function($) {
    $('.btn-import').on('click', function() {
        const btn = $(this);
        const type = btn.data('import-type');
        const resultDiv = $(`#${type}-result`);
        const logDiv = $('#import-log');
        
        // Disable button
        btn.prop('disabled', true);
        btn.html('<span class="loading-spinner"></span> در حال ایمپورت...');
        
        // Clear previous result
        resultDiv.html('');
        
        // AJAX request
        $.ajax({
            url: zargarAjax.ajaxurl,
            type: 'POST',
            data: {
                action: `zargar_import_${type}`,
                nonce: zargarAjax.importNonce
            },
            success: function(response) {
                if (response.success) {
                    const data = response.data;
                    let detailsHtml = '';
                    
                    if (type === 'metadata' && data.fields) {
                        detailsHtml = `
                            <div class="import-details">
                                <strong>فیلدهای ثبت شده:</strong>
                                <ul class="import-fields-list">
                                    ${data.fields.map(f => `<li>${f.label} (${f.key})</li>`).join('')}
                                </ul>
                            </div>
                        `;
                    } else if (type === 'attributes' && data.counts) {
                        detailsHtml = `
                            <div class="import-stats">
                                <div class="stat-item success">
                                    <i class="lni lni-checkmark"></i>
                                    ${data.counts.created} ایجاد شد
                                </div>
                                <div class="stat-item info">
                                    <i class="lni lni-flag"></i>
                                    ${data.counts.skipped} موجود بود
                                </div>
                                ${data.counts.errors > 0 ? `
                                <div class="stat-item error">
                                    <i class="lni lni-close"></i>
                                    ${data.counts.errors} خطا
                                </div>
                                ` : ''}
                            </div>
                        `;
                    }
                    
                    resultDiv.html(`
                        <div class="import-success">
                            <i class="lni lni-checkmark-circle"></i>
                            <span>${data.message}</span>
                        </div>
                        ${detailsHtml}
                    `);
                    
                    // Add to log
                    const time = new Date().toLocaleTimeString('fa-IR');
                    if (logDiv.find('.import-log-empty').length) {
                        logDiv.html('');
                    }
                    logDiv.prepend(`
                        <div class="import-log-item success">
                            <span class="log-time">${time}</span>
                            <span class="log-icon"><i class="lni lni-checkmark-circle"></i></span>
                            <span class="log-message">${data.message}</span>
                        </div>
                    `);
                } else {
                    resultDiv.html(`
                        <div class="import-error">
                            <i class="lni lni-close"></i>
                            <span>${response.data.message}</span>
                        </div>
                    `);
                    
                    // Add to log
                    const time = new Date().toLocaleTimeString('fa-IR');
                    if (logDiv.find('.import-log-empty').length) {
                        logDiv.html('');
                    }
                    logDiv.prepend(`
                        <div class="import-log-item error">
                            <span class="log-time">${time}</span>
                            <span class="log-icon"><i class="lni lni-close"></i></span>
                            <span class="log-message">خطا: ${response.data.message}</span>
                        </div>
                    `);
                }
            },
            error: function() {
                resultDiv.html(`
                    <div class="import-error">
                        <i class="lni lni-close"></i>
                        <span>خطا در ارتباط با سرور</span>
                    </div>
                `);
            },
            complete: function() {
                // Re-enable button
                btn.prop('disabled', false);
                const icon = type === 'metadata' ? 'database' : 'tag';
                btn.html(`<i class="lni lni-${icon}"></i> ایمپورت مجدد`);
            }
        });
    });
});
</script>
