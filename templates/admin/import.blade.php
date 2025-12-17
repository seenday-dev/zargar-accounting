@include('components.header', ['title' => 'ایمپورت'])

<div class="zargar-content-wrapper">
    <aside class="zargar-sidebar-wrapper">
        @include('components.sidebar')
    </aside>
    
    <main class="zargar-main-content">
        <div class="content-inner">
            <h2 class="page-title" style="font-size: 28px; color: var(--gold-400); margin-bottom: var(--space-xl);">ایمپورت داده‌ها از سیستم زرگر</h2>
            
            <!-- Product Import Section -->
            <div class="import-section">
                <div class="import-header">
                    <div class="import-header-icon">
                        <i class="lni lni-cart"></i>
                    </div>
                    <div>
                        <h3>ایمپورت محصولات</h3>
                        <p>انتخاب فیلدهای دلخواه برای ایمپورت از API زرگر به WooCommerce</p>
                    </div>
                </div>
                
                <!-- Import Mode Tabs -->
                <div class="import-mode-tabs">
                    <button class="import-mode-tab active" data-mode="bulk">
                        <i class="lni lni-layers"></i>
                        <span>ایمپورت دسته‌ای</span>
                        <small>همه محصولات</small>
                    </button>
                    <button class="import-mode-tab" data-mode="specific">
                        <i class="lni lni-search"></i>
                        <span>ایمپورت انتخابی</span>
                        <small>با کد محصول</small>
                    </button>
                </div>
                
                <!-- Stats Box -->
                <div class="import-stats-box" id="import-stats">
                    <div class="stats-loading">
                        <i class="lni lni-spinner lni-is-spinning"></i>
                        در حال بارگذاری اطلاعات از سرور زرگر...
                    </div>
                </div>
                
                <!-- Specific Product Import -->
                <div class="specific-import-container" id="specific-import" style="display: none;">
                    <div class="specific-import-header">
                        <i class="lni lni-magnifier"></i>
                        <div>
                            <h4>ایمپورت محصولات خاص</h4>
                            <p>کد محصول (ProductCode) را وارد کنید. برای چند محصول از کاما استفاده کنید.</p>
                        </div>
                    </div>
                    
                    <div class="product-code-input-wrapper">
                        <div class="input-group">
                            <label for="product-codes">
                                <i class="lni lni-tag"></i>
                                کد محصولات
                            </label>
                            <input 
                                type="text" 
                                id="product-codes" 
                                class="product-code-input" 
                                placeholder="مثال: GD01000377, GD01000378, GD01000379"
                                autocomplete="off"
                            >
                            <small class="input-hint">
                                <i class="lni lni-information"></i>
                                برای جداسازی کدهای محصول از کاما (,) استفاده کنید
                            </small>
                        </div>
                        
                        <div class="product-code-actions">
                            <button class="btn btn-primary" id="search-products">
                                <i class="lni lni-search"></i>
                                جستجو و نمایش
                            </button>
                            <button class="btn btn-secondary" id="clear-codes">
                                <i class="lni lni-close"></i>
                                پاک کردن
                            </button>
                        </div>
                    </div>
                    
                    <!-- Search Results -->
                    <div class="search-results" id="search-results" style="display: none;">
                        <div class="results-header">
                            <div class="results-count">
                                <i class="lni lni-check-box"></i>
                                <span id="results-count-text">0 محصول یافت شد</span>
                            </div>
                        </div>
                        
                        <div class="results-grid" id="results-grid"></div>
                        
                        <!-- Field Selection for Specific Products -->
                        <div class="specific-field-selection" id="specific-field-selection" style="margin-top: 24px;">
                            <div class="field-selection-header">
                                <h4>انتخاب فیلدها برای ایمپورت</h4>
                                <div class="field-selection-actions">
                                    <button class="btn-link" id="select-all-specific">انتخاب همه</button>
                                    <button class="btn-link" id="deselect-all-specific">حذف انتخاب همه</button>
                                </div>
                            </div>
                            
                            <div class="field-categories" id="field-categories-specific"></div>
                            
                            <div class="import-actions">
                                <button class="btn btn-success btn-large" id="import-selected-products">
                                    <i class="lni lni-download"></i>
                                    ایمپورت محصولات انتخابی
                                </button>
                                <span class="selected-count" id="selected-count-specific">0 فیلد انتخاب شده</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Field Selection -->
                <div class="field-selection-container" id="field-selection" style="display: none;">
                    <div class="field-selection-header">
                        <h4>انتخاب فیلدها برای Import</h4>
                        <div class="field-selection-actions">
                            <button class="btn-link" id="select-all">انتخاب همه</button>
                            <button class="btn-link" id="deselect-all">حذف انتخاب همه</button>
                        </div>
                    </div>
                    
                    <div class="field-categories" id="field-categories"></div>
                    
                    <div class="import-actions">
                        <button class="btn btn-primary btn-large" id="start-import">
                            <i class="lni lni-download"></i>
                            شروع ایمپورت
                        </button>
                        <span class="selected-count" id="selected-count">0 فیلد انتخاب شده</span>
                    </div>
                </div>
                
                <!-- Progress Section -->
                <div class="import-progress-container" id="import-progress" style="display: none;">
                    <div class="progress-header">
                        <h4>پیشرفت ایمپورت</h4>
                        <button class="btn-icon" id="cancel-import" title="لغو">
                            <i class="lni lni-close"></i>
                        </button>
                    </div>
                    
                    <div class="progress-bar-container">
                        <div class="progress-bar" id="progress-bar"></div>
                    </div>
                    
                    <div class="progress-stats">
                        <div class="progress-stat">
                            <span class="progress-label">کل:</span>
                            <span class="progress-value" id="progress-total">0</span>
                        </div>
                        <div class="progress-stat success">
                            <span class="progress-label">موفق:</span>
                            <span class="progress-value" id="progress-imported">0</span>
                        </div>
                        <div class="progress-stat error">
                            <span class="progress-label">ناموفق:</span>
                            <span class="progress-value" id="progress-failed">0</span>
                        </div>
                    </div>
                    
                    <div class="progress-message" id="progress-message">
                        در حال آماده‌سازی...
                    </div>
                </div>
            </div>
            
            <!-- Import Logs Section -->
            <div class="import-logs-section">
                <div class="logs-header">
                    <h3>
                        <i class="lni lni-text-format"></i>
                        لاگ عملیات ایمپورت
                    </h3>
                    <button class="btn btn-secondary btn-small" id="clear-logs">
                        <i class="lni lni-trash"></i>
                        پاک کردن لاگ‌ها
                    </button>
                </div>
                
                <div class="logs-container" id="import-logs">
                    <div class="logs-empty">
                        <i class="lni lni-empty-file"></i>
                        <p>هنوز لاگی ثبت نشده است</p>
                    </div>
                </div>
            </div>
            
            <!-- Quick Import Cards -->
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
                        ایجاد ۸ ویژگی WooCommerce شامل وزن، وزن پایه، مدل، مجموعه، رنگ، سایز، نرخ وزن و اجرت(درصد)
                    </p>
                    <button class="btn btn-primary btn-import" data-import-type="attributes">
                        <i class="lni lni-download"></i>
                        ایمپورت ویژگی‌ها
                    </button>
                    <div class="import-result" id="attributes-result"></div>
                </div>
            </div>
        </div>
    </main>
</div>

@include('components.footer')

<script>
jQuery(document).ready(function($) {
    // Helper function to add log entry
    function addLogEntry(message, type = 'info', details = null) {
        const logDiv = $('#import-logs');
        const time = new Date().toLocaleTimeString('fa-IR');
        
        // Remove empty state
        if (logDiv.find('.logs-empty').length) {
            logDiv.html('');
        }
        
        const iconMap = {
            success: 'checkmark-circle',
            error: 'close-circle',
            info: 'information',
            warning: 'warning'
        };
        
        const logHtml = `
            <div class="log-entry log-${type}">
                <span class="log-time">${time}</span>
                <span class="log-icon"><i class="lni lni-${iconMap[type] || 'information'}"></i></span>
                <span class="log-message">${message}</span>
                ${details ? `<div class="log-details">${details}</div>` : ''}
            </div>
        `;
        
        logDiv.prepend(logHtml);
        
        // Scroll to top
        logDiv.scrollTop(0);
    }
    
    // Import buttons handler
    $('.btn-import').on('click', function() {
        const btn = $(this);
        const type = btn.data('import-type');
        const resultDiv = $(`#${type}-result`);
        const typeLabel = type === 'metadata' ? 'متادیتا' : 'ویژگی‌ها';
        
        // Disable button
        btn.prop('disabled', true);
        btn.html('<i class="lni lni-spinner lni-is-spinning"></i> در حال ایمپورت...');
        
        // Clear previous result
        resultDiv.html('');
        
        // Add start log
        addLogEntry(`شروع ایمپورت ${typeLabel}...`, 'info');
        
        // AJAX request
        $.ajax({
            url: zargarAjax.ajaxurl,
            type: 'POST',
            data: {
                action: `zargar_import_${type}`,
                nonce: zargarAjax.importNonce
            },
            timeout: 60000, // 60 seconds timeout
            success: function(response) {
                if (response && response.success) {
                    const data = response.data || {};
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
                        
                        // Add detailed log
                        const fieldsList = data.fields.map(f => `• ${f.label}`).join('<br>');
                        addLogEntry(
                            `ایمپورت ${data.fields.length} فیلد متادیتا با موفقیت انجام شد`,
                            'success',
                            `<small>${fieldsList}</small>`
                        );
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
                        
                        // Add detailed log
                        addLogEntry(
                            `ایمپورت ویژگی‌ها انجام شد: ${data.counts.created} ایجاد، ${data.counts.skipped} موجود`,
                            data.counts.errors > 0 ? 'warning' : 'success'
                        );
                    } else {
                        // Generic success
                        addLogEntry(data.message || `${typeLabel} با موفقیت ایمپورت شد`, 'success');
                    }
                    
                    resultDiv.html(`
                        <div class="import-success">
                            <i class="lni lni-checkmark-circle"></i>
                            <span>${data.message || 'عملیات موفق'}</span>
                        </div>
                        ${detailsHtml}
                    `);
                } else {
                    const errorMsg = (response && response.data && response.data.message) || 'خطای نامشخص';
                    
                    resultDiv.html(`
                        <div class="import-error">
                            <i class="lni lni-close"></i>
                            <span>${errorMsg}</span>
                        </div>
                    `);
                    
                    addLogEntry(`خطا در ایمپورت ${typeLabel}: ${errorMsg}`, 'error');
                }
            },
            error: function(xhr, status, error) {
                let errorMsg = 'خطا در ارتباط با سرور';
                
                if (status === 'timeout') {
                    errorMsg = 'زمان اتصال به سرور به پایان رسید';
                } else if (status === 'parsererror') {
                    errorMsg = 'خطا در پردازش پاسخ سرور';
                } else if (xhr.status === 0) {
                    errorMsg = 'عدم اتصال به اینترنت';
                } else if (xhr.status >= 500) {
                    errorMsg = 'خطای داخلی سرور';
                } else if (xhr.status >= 400) {
                    errorMsg = 'درخواست نامعتبر';
                }
                
                resultDiv.html(`
                    <div class="import-error">
                        <i class="lni lni-close"></i>
                        <span>${errorMsg}</span>
                    </div>
                `);
                
                addLogEntry(
                    `خطای شبکه در ایمپورت ${typeLabel}`,
                    'error',
                    `<small>Status: ${xhr.status}, Error: ${error || 'Unknown'}</small>`
                );
            },
            complete: function() {
                // Re-enable button
                btn.prop('disabled', false);
                const icon = type === 'metadata' ? 'database' : 'tag';
                btn.html(`<i class="lni lni-${icon}"></i> ایمپورت مجدد`);
            }
        });
    });
    
    // Clear logs button
    $('#clear-logs').on('click', function() {
        if (confirm('آیا از پاک کردن تمام لاگ‌ها مطمئن هستید؟')) {
            $('#import-logs').html(`
                <div class="logs-empty">
                    <i class="lni lni-empty-file"></i>
                    <p>هنوز لاگی ثبت نشده است</p>
                </div>
            `);
            addLogEntry('لاگ‌ها پاک شدند', 'info');
        }
    });
});
</script>
