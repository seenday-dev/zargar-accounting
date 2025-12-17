/**
 * Zargar Accounting - Product Import
 * مدیریت ایمپورت محصولات با انتخاب فیلد
 */

(function($) {
    'use strict';
    
    let selectedFields = [];
    let importInterval = null;
    let isImporting = false;
    
    $(document).ready(function() {
        
        // بارگذاری آمار اولیه
        loadImportStats();
        
        // Import Mode Tabs
        $('.import-mode-tab').on('click', function() {
            const mode = $(this).data('mode');
            $('.import-mode-tab').removeClass('active');
            $(this).addClass('active');
            
            if (mode === 'bulk') {
                $('#field-selection').slideDown();
                $('#specific-import').slideUp();
                addLogEntry('حالت ایمپورت دسته‌ای فعال شد', 'info');
            } else {
                $('#field-selection').slideUp();
                $('#specific-import').slideDown();
                $('#search-results').hide();
                addLogEntry('حالت ایمپورت انتخابی فعال شد', 'info');
            }
        });
        
        // Search Products
        $('#search-products').on('click', function() {
            searchProducts();
        });
        
        // Clear Product Codes
        $('#clear-codes').on('click', function() {
            $('#product-codes').val('');
            $('#search-results').slideUp();
        });
        
        // Import Selected Products
        $('#import-selected-products').on('click', function() {
            importSelectedProducts();
        });
        
        // انتخاب همه فیلدها
        $('#select-all').on('click', function() {
            $('.field-checkbox').prop('checked', true);
            updateSelectedCount();
        });
        
        // حذف انتخاب همه
        $('#deselect-all').on('click', function() {
            $('.field-checkbox').prop('checked', false);
            updateSelectedCount();
        });
        
        // تغییر checkbox ها
        $(document).on('change', '.field-checkbox', function() {
            updateSelectedCount();
        });
        
        // Product card selection
        $(document).on('click', '.result-product-card', function(e) {
            if (e.target.type !== 'checkbox') {
                const checkbox = $(this).find('.product-card-checkbox');
                checkbox.prop('checked', !checkbox.prop('checked')).trigger('change');
            }
        });
        
        $(document).on('change', '.product-card-checkbox', function() {
            $(this).closest('.result-product-card').toggleClass('selected', $(this).is(':checked'));
            updateSelectedProductsCount();
        });
        
        // شروع ایمپورت
        $('#start-import').on('click', function() {
            startImport();
        });
        
        // لغو ایمپورت
        $('#cancel-import').on('click', function() {
            cancelImport();
        });
        
        // پاک کردن لاگ‌ها
        $('#clear-logs').on('click', function() {
            clearLogs();
        });
        
    });
    
    /**
     * بارگذاری آمار اولیه
     */
    function loadImportStats() {
        $.ajax({
            url: zargarAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'zargar_get_import_stats',
                nonce: zargarAjax.importNonce
            },
            success: function(response) {
                if (response.success) {
                    displayStats(response.data);
                    renderFieldSelection(response.data.available_fields);
                } else {
                    showError(response.data.message);
                }
            },
            error: function() {
                showError('خطا در ارتباط با سرور');
            }
        });
    }
    
    /**
     * نمایش آمار
     */
    function displayStats(data) {
        const html = `
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-card-label">محصولات موجود در سرور</div>
                    <div class="stat-card-value">
                        <i class="lni lni-package"></i>
                        ${data.total}
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-label">فیلدهای قابل انتخاب</div>
                    <div class="stat-card-value">
                        <i class="lni lni-list"></i>
                        ${Object.keys(data.available_fields || {}).reduce((sum, key) => sum + Object.keys(data.available_fields[key].fields).length, 0)}
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-label">دسته‌بندی فیلدها</div>
                    <div class="stat-card-value">
                        <i class="lni lni-grid-alt"></i>
                        ${Object.keys(data.available_fields || {}).length}
                    </div>
                </div>
            </div>
        `;
        
        $('#import-stats').html(html).fadeIn();
        $('#field-selection').fadeIn();
        
        // ذخیره availableFields برای استفاده در بخش specific
        window.zargarAvailableFields = data.available_fields;
        
        // Add log entry
        addLogEntry('اطلاعات سرور بارگذاری شد: ' + data.total + ' محصول آماده', 'success');
    }
    
    /**
     * رندر کردن فیلدها برای انتخاب
     */
    function renderFieldSelection(availableFields) {
        let html = '';
        
        $.each(availableFields, function(categoryKey, category) {
            html += `
                <div class="field-category">
                    <div class="field-category-header">
                        <h5>${category.title}</h5>
                        <label class="checkbox-label">
                            <input type="checkbox" class="category-checkbox" data-category="${categoryKey}">
                            <span>انتخاب همه</span>
                        </label>
                    </div>
                    <div class="field-category-items">
            `;
            
            $.each(category.fields, function(fieldKey, field) {
                html += `
                    <label class="field-item">
                        <input type="checkbox" class="field-checkbox" value="${fieldKey}" data-category="${categoryKey}">
                        <span class="field-label">${field.label}</span>
                        <span class="field-target">${field.target}</span>
                    </label>
                `;
            });
            
            html += `
                    </div>
                </div>
            `;
        });
        
        $('#field-categories').html(html);
        
        // انتخاب دسته کامل
        $(document).on('change', '.category-checkbox', function() {
            const category = $(this).data('category');
            const checked = $(this).is(':checked');
            $(`.field-checkbox[data-category="${category}"]`).prop('checked', checked);
            updateSelectedCount();
        });
    }
    
    /**
     * به‌روزرسانی تعداد فیلدهای انتخاب شده
     */
    function updateSelectedCount() {
        const count = $('.field-checkbox:checked').length;
        $('#selected-count').text(count + ' فیلد انتخاب شده');
        
        // فعال/غیرفعال کردن دکمه import
        $('#start-import').prop('disabled', count === 0);
    }
    
    /**
     * شروع ایمپورت
     */
    function startImport() {
        selectedFields = [];
        $('.field-checkbox:checked').each(function() {
            selectedFields.push($(this).val());
        });
        
        if (selectedFields.length === 0) {
            alert('لطفاً حداقل یک فیلد انتخاب کنید');
            addLogEntry('خطا: هیچ فیلدی انتخاب نشده است', 'error');
            return;
        }
        
        if (!confirm(`آیا مطمئن هستید که می‌خواهید ${selectedFields.length} فیلد را import کنید؟`)) {
            addLogEntry('عملیات ایمپورت توسط کاربر لغو شد', 'info');
            return;
        }
        
        isImporting = true;
        $('#field-selection').slideUp();
        $('#import-progress').slideDown();
        
        addLogEntry(`شروع ایمپورت ${selectedFields.length} فیلد...`, 'info');
        
        // ارسال درخواست شروع import
        $.ajax({
            url: zargarAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'zargar_start_import',
                nonce: zargarAjax.importNonce,
                fields: JSON.stringify(selectedFields)
            },
            timeout: 60000,
            success: function(response) {
                if (response && response.success) {
                    addLogEntry('ایمپورت با موفقیت شروع شد', 'success');
                    // شروع polling برای بررسی پیشرفت
                    startProgressPolling();
                } else {
                    const errorMsg = (response && response.data && response.data.message) || 'خطای نامشخص';
                    showError(errorMsg);
                    addLogEntry('خطا در شروع ایمپورت: ' + errorMsg, 'error');
                    isImporting = false;
                    $('#import-progress').slideUp();
                    $('#field-selection').slideDown();
                }
            },
            error: function(xhr, status, error) {
                let errorMsg = 'خطا در ارتباط با سرور';
                if (status === 'timeout') {
                    errorMsg = 'زمان اتصال به پایان رسید';
                } else if (xhr.status >= 500) {
                    errorMsg = 'خطای داخلی سرور';
                }
                showError(errorMsg);
                addLogEntry('خطای شبکه: ' + errorMsg, 'error');
                isImporting = false;
                $('#import-progress').slideUp();
                $('#field-selection').slideDown();
            }
        });
    }
    
    /**
     * شروع polling برای چک کردن پیشرفت
     */
    function startProgressPolling() {
        importInterval = setInterval(function() {
            checkProgress();
            loadLogs();
        }, 2000); // هر 2 ثانیه
    }
    
    /**
     * چک کردن پیشرفت import
     */
    function checkProgress() {
        $.ajax({
            url: zargarAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'zargar_get_import_progress',
                nonce: zargarAjax.importNonce
            },
            success: function(response) {
                if (response.success) {
                    updateProgress(response.data);
                }
            }
        });
    }
    
    /**
     * به‌روزرسانی نمایش پیشرفت
     */
    function updateProgress(data) {
        const total = data.total || 0;
        const imported = data.imported || 0;
        const failed = data.failed || 0;
        const percentage = total > 0 ? Math.round((imported + failed) / total * 100) : 0;
        
        // Progress bar
        $('#progress-bar').css('width', percentage + '%');
        
        // آمار
        $('#progress-total').text(total);
        $('#progress-imported').text(imported);
        $('#progress-failed').text(failed);
        
        // پیام
        $('#progress-message').text(data.message || '');
        
        // لاگ پیشرفت (هر 10 محصول)
        if (imported > 0 && imported % 10 === 0) {
            addLogEntry(`${imported} محصول از ${total} ایمپورت شد (${percentage}%)`, 'info');
        }
        
        // اگر تکمیل شد
        if (data.status === 'completed' || data.status === 'error') {
            clearInterval(importInterval);
            isImporting = false;
            
            if (data.status === 'completed') {
                addLogEntry(
                    `ایمپورت با موفقیت تکمیل شد!`,
                    'success',
                    `<small>✓ ${imported} موفق | ✗ ${failed} ناموفق | ═ ${total} کل</small>`
                );
            } else {
                addLogEntry('خطا در تکمیل ایمپورت: ' + data.message, 'error');
            }
            
            setTimeout(function() {
                $('#import-progress').slideUp();
                $('#field-selection').slideDown();
                
                if (data.status === 'completed') {
                    alert(`ایمپورت با موفقیت تکمیل شد!\n\nموفق: ${imported}\nناموفق: ${failed}`);
                } else {
                    alert('خطا در ایمپورت: ' + data.message);
                }
            }, 3000);
        }
    }
    
    /**
     * لغو ایمپورت
     */
    function cancelImport() {
        if (confirm('آیا مطمئن هستید که می‌خواهید ایمپورت را لغو کنید؟')) {
            clearInterval(importInterval);
            isImporting = false;
            
            $('#import-progress').slideUp();
            $('#field-selection').slideDown();
        }
    }
    
    /**
     * افزودن ورودی لاگ
     */
    function addLogEntry(message, type, details) {
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
            <div class="log-entry log-${type || 'info'}">
                <span class="log-time">${time}</span>
                <span class="log-icon"><i class="lni lni-${iconMap[type] || 'information'}"></i></span>
                <span class="log-message">${message}</span>
                ${details ? `<div class="log-details">${details}</div>` : ''}
            </div>
        `;
        
        logDiv.prepend(logHtml);
        logDiv.scrollTop(0);
    }
    
    /**
     * بارگذاری لاگ‌ها
     */
    function loadLogs() {
        // بارگذاری لاگ‌های جدید در حین import
        
        // برای الان از فایل لاگ موجود استفاده می‌کنیم
        $.ajax({
            url: zargarAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'zargar_get_logs',
                nonce: zargarAjax.nonce,
                type: 'product',
                level: 'all'
            },
            success: function(response) {
                if (response.success && response.data.logs) {
                    displayLogs(response.data.logs);
                }
            }
        });
    }
    
    /**
     * نمایش لاگ‌ها
     */
    function displayLogs(logs) {
        if (!logs || logs.length === 0) {
            return;
        }
        
        let html = '<div class="log-entries">';
        
        logs.slice(-20).forEach(function(log) {
            const levelClass = log.level ? log.level.toLowerCase() : 'info';
            html += `
                <div class="log-entry log-${levelClass}">
                    <span class="log-time">${log.datetime || ''}</span>
                    <span class="log-level">${log.level || 'INFO'}</span>
                    <span class="log-message">${log.message || ''}</span>
                </div>
            `;
        });
        
        html += '</div>';
        
        $('#import-logs').html(html);
    }
    
    /**
     * پاک کردن لاگ‌ها
     */
    function clearLogs() {
        if (!confirm('آیا مطمئن هستید که می‌خواهید تمام لاگ‌ها را پاک کنید؟')) {
            return;
        }
        
        $.ajax({
            url: zargarAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'zargar_clear_import_logs',
                nonce: zargarAjax.importNonce
            },
            success: function(response) {
                if (response.success) {
                    $('#import-logs').html(`
                        <div class="logs-empty">
                            <i class="lni lni-empty-file"></i>
                            <p>هنوز لاگی ثبت نشده است</p>
                        </div>
                    `);
                    addLogEntry('تمام لاگ‌ها پاک شدند', 'info');
                } else {
                    addLogEntry('خطا در پاک کردن لاگ‌ها', 'error');
                }
            },
            error: function() {
                addLogEntry('خطای شبکه در پاک کردن لاگ‌ها', 'error');
            }
        });
    }
    
    /**
     * جستجوی محصولات بر اساس کد
     */
    function searchProducts() {
        const codesInput = $('#product-codes').val().trim();
        
        if (!codesInput) {
            alert('لطفاً حداقل یک کد محصول وارد کنید');
            addLogEntry('خطا: کد محصول وارد نشده است', 'error');
            return;
        }
        
        // Parse codes
        const codes = codesInput.split(',').map(c => c.trim()).filter(c => c);
        
        if (codes.length === 0) {
            alert('کد محصول معتبر یافت نشد');
            return;
        }
        
        addLogEntry(`جستجوی ${codes.length} کد محصول...`, 'info');
        
        // Check if zargarAjax is defined
        if (typeof zargarAjax === 'undefined') {
            $('#search-results').show();
            $('#results-grid').html(`
                <div class="error-message">
                    <i class="lni lni-close"></i>
                    <h4>خطای پیکربندی</h4>
                    <p><strong>zargarAjax تعریف نشده است</strong></p>
                    <pre>لطفاً مطمئن شوید که اسکریپت‌ها به درستی بارگذاری شده‌اند.</pre>
                </div>
            `);
            addLogEntry('خطا: zargarAjax تعریف نشده است', 'error');
            console.error('zargarAjax is undefined');
            return;
        }
        
        // Log AJAX parameters
        console.log('AJAX Request Parameters:', {
            url: zargarAjax.ajaxurl,
            action: 'zargar_search_products',
            nonce: zargarAjax.importNonce,
            codes: codes
        });
        
        // Show loading
        $('#search-results').show();
        $('#results-grid').html('<div class="loading-spinner"><i class="lni lni-spinner lni-is-spinning"></i> در حال جستجو...</div>');
        
        // AJAX request
        $.ajax({
            url: zargarAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'zargar_search_products',
                nonce: zargarAjax.importNonce,
                codes: JSON.stringify(codes)
            },
            timeout: 30000,
            beforeSend: function() {
                console.log('AJAX request started...');
                addLogEntry('ارسال درخواست به سرور...', 'info');
            },
            success: function(response) {
                console.log('AJAX Response received:', response);
                
                if (response && response.success) {
                    console.log('Search successful, products found:', response.data.products.length);
                    displaySearchResults(response.data.products);
                    addLogEntry(
                        `${response.data.products.length} محصول یافت شد از ${codes.length} کد جستجو شده`,
                        'success'
                    );
                } else {
                    console.error('Search failed:', response);
                    const errorMessage = response.data && response.data.message ? response.data.message : 'خطا در جستجو';
                    const errorDetails = response.data && response.data.details ? response.data.details : '';
                    
                    console.error('Error Message:', errorMessage);
                    console.error('Error Details:', errorDetails);
                    
                    $('#results-grid').html(`
                        <div class="error-message">
                            <i class="lni lni-close"></i>
                            <h4>خطا در جستجوی محصولات</h4>
                            <p><strong>پیام:</strong> ${errorMessage}</p>
                            ${errorDetails ? `<pre style="background:#f5f5f5;padding:10px;margin-top:10px;border-radius:5px;text-align:left;direction:ltr;font-size:12px;overflow-x:auto;">${errorDetails}</pre>` : ''}
                        </div>
                    `);
                    addLogEntry('خطا در جستجوی محصولات: ' + errorMessage, 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', {
                    status: status,
                    error: error,
                    xhr: xhr,
                    readyState: xhr.readyState,
                    statusCode: xhr.status,
                    statusText: xhr.statusText,
                    responseText: xhr.responseText
                });
                
                let errorMsg = 'خطا در ارتباط با سرور';
                let errorDetails = `Status: ${status}\nError: ${error}\nHTTP Code: ${xhr.status}\nReady State: ${xhr.readyState}`;
                
                if (status === 'timeout') {
                    errorMsg = 'زمان جستجو به پایان رسید (بیش از 30 ثانیه)';
                } else if (status === 'error' && xhr.status === 0) {
                    errorMsg = 'عدم اتصال به سرور وردپرس';
                    errorDetails += '\n\nدلایل احتمالی:\n';
                    errorDetails += '1. سرور وردپرس خاموش است\n';
                    errorDetails += '2. مشکل در تنظیمات شبکه\n';
                    errorDetails += '3. فایروال درخواست را مسدود کرده\n';
                    errorDetails += '4. آدرس AJAX نادرست است: ' + (typeof zargarAjax !== 'undefined' ? zargarAjax.ajaxurl : 'undefined');
                } else if (status === 'parsererror') {
                    errorMsg = 'خطا در تجزیه پاسخ سرور (JSON نامعتبر)';
                    errorDetails += '\n\nپاسخ سرور JSON معتبر نیست';
                } else if (xhr.status >= 500) {
                    errorMsg = 'خطای داخلی سرور وردپرس (500)';
                    errorDetails += '\n\nسرور با خطای داخلی مواجه شده است';
                } else if (xhr.status === 403) {
                    errorMsg = 'دسترسی غیرمجاز (403 Forbidden)';
                    errorDetails += '\n\nممکن است مشکل از nonce یا سطح دسترسی باشد';
                } else if (xhr.status === 404) {
                    errorMsg = 'آدرس AJAX یافت نشد (404)';
                    errorDetails += '\n\nآدرس: ' + (typeof zargarAjax !== 'undefined' ? zargarAjax.ajaxurl : 'undefined');
                } else {
                    errorDetails += `\nHTTP ${xhr.status}: ${xhr.statusText}`;
                }
                
                try {
                    const responseText = xhr.responseText || '';
                    if (responseText) {
                        errorDetails += '\n\n=== پاسخ سرور ===\n' + responseText.substring(0, 1000);
                    }
                } catch (e) {}
                
                $('#results-grid').html(`
                    <div class="error-message">
                        <i class="lni lni-close"></i>
                        <h4>${errorMsg}</h4>
                        ${errorDetails ? `<pre style="background:#f5f5f5;padding:10px;margin-top:10px;border-radius:5px;text-align:left;direction:ltr;font-size:12px;overflow-x:auto;">${errorDetails}</pre>` : ''}
                    </div>
                `);
                addLogEntry('خطای شبکه: ' + errorMsg + (errorDetails ? ' - ' + errorDetails : ''), 'error');
            }
        });
    }
    
    /**
     * نمایش نتایج جستجو
     */
    function displaySearchResults(products) {
        if (!products || products.length === 0) {
            $('#results-grid').html(`
                <div class="empty-results">
                    <i class="lni lni-inbox"></i>
                    <p>هیچ محصولی با کدهای وارد شده یافت نشد</p>
                </div>
            `);
            $('#results-count-text').text('0 محصول یافت شد');
            return;
        }
        
        let html = '';
        products.forEach(function(product) {
            html += `
                <div class="result-product-card" data-product-id="${product.ProductId}">
                    <div class="product-card-header">
                        <span class="product-card-code">${product.ProductCode}</span>
                        <input type="checkbox" class="product-card-checkbox" value="${product.ProductCode}">
                    </div>
                    <div class="product-card-title">${product.ProductTitle || 'بدون عنوان'}</div>
                    <div class="product-card-meta">
                        ${product.CategoryTitle ? `
                            <div class="product-card-meta-item">
                                <i class="lni lni-tag"></i>
                                ${product.CategoryTitle}
                            </div>
                        ` : ''}
                        ${product.Weight ? `
                            <div class="product-card-meta-item">
                                <i class="lni lni-weight"></i>
                                وزن: ${product.Weight} گرم
                            </div>
                        ` : ''}
                        ${product.GoldPrice ? `
                            <div class="product-card-meta-item">
                                <i class="lni lni-coin"></i>
                                قیمت: ${Number(product.GoldPrice).toLocaleString('fa-IR')} ریال
                            </div>
                        ` : ''}
                    </div>
                </div>
            `;
        });
        
        $('#results-grid').html(html);
        $('#results-count-text').text(`${products.length} محصول یافت شد`);
        
        // رندر فیلدها برای ایمپورت انتخابی
        renderFieldSelectionForSpecific();
    }
    
    /**
     * رندر کردن فیلدها برای ایمپورت انتخابی
     */
    function renderFieldSelectionForSpecific() {
        // استفاده از همان availableFields که قبلاً بارگذاری شده
        const availableFields = window.zargarAvailableFields || {};
        
        if (Object.keys(availableFields).length === 0) {
            // اگر فیلدها بارگذاری نشده‌اند، آنها را دریافت کنیم
            $.ajax({
                url: zargarAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'zargar_get_import_stats',
                    nonce: zargarAjax.importNonce
                },
                success: function(response) {
                    if (response.success && response.data.available_fields) {
                        window.zargarAvailableFields = response.data.available_fields;
                        renderFieldsInSpecificSection(response.data.available_fields);
                    }
                }
            });
        } else {
            renderFieldsInSpecificSection(availableFields);
        }
    }
    
    /**
     * رندر کردن فیلدها در بخش specific
     */
    function renderFieldsInSpecificSection(availableFields) {
        let html = '';
        
        $.each(availableFields, function(categoryKey, category) {
            html += `
                <div class="field-category">
                    <div class="field-category-header">
                        <h5>${category.title}</h5>
                        <label class="checkbox-label">
                            <input type="checkbox" class="category-checkbox-specific" data-category="${categoryKey}">
                            <span>انتخاب همه</span>
                        </label>
                    </div>
                    <div class="field-category-items">
            `;
            
            $.each(category.fields, function(fieldKey, field) {
                html += `
                    <label class="field-item">
                        <input type="checkbox" class="field-checkbox field-checkbox-specific" value="${fieldKey}" data-category="${categoryKey}">
                        <span class="field-label">${field.label}</span>
                        <span class="field-target">${field.target}</span>
                    </label>
                `;
            });
            
            html += `
                    </div>
                </div>
            `;
        });
        
        $('#field-categories-specific').html(html);
        
        // انتخاب دسته کامل
        $(document).off('change', '.category-checkbox-specific').on('change', '.category-checkbox-specific', function() {
            const category = $(this).data('category');
            const checked = $(this).is(':checked');
            $(`.field-checkbox-specific[data-category="${category}"]`).prop('checked', checked);
            updateSelectedCountSpecific();
        });
        
        // تغییر checkbox ها
        $(document).off('change', '.field-checkbox-specific').on('change', '.field-checkbox-specific', function() {
            updateSelectedCountSpecific();
        });
        
        // انتخاب همه برای specific
        $('#select-all-specific').off('click').on('click', function() {
            $('.field-checkbox-specific').prop('checked', true);
            updateSelectedCountSpecific();
        });
        
        // حذف انتخاب همه برای specific
        $('#deselect-all-specific').off('click').on('click', function() {
            $('.field-checkbox-specific').prop('checked', false);
            updateSelectedCountSpecific();
        });
    }
    
    /**
     * به‌روزرسانی تعداد فیلدهای انتخاب شده (specific)
     */
    function updateSelectedCountSpecific() {
        const count = $('.field-checkbox-specific:checked').length;
        $('#selected-count-specific').text(count + ' فیلد انتخاب شده');
    }
    
    /**
     * به‌روزرسانی تعداد محصولات انتخاب شده
     */
    function updateSelectedProductsCount() {
        const count = $('.product-card-checkbox:checked').length;
        $('#results-count-text').text(`${count} محصول انتخاب شده`);
    }
    
    /**
     * ایمپورت محصولات انتخابی
     */
    function importSelectedProducts() {
        const selectedCodes = [];
        $('.product-card-checkbox:checked').each(function() {
            selectedCodes.push($(this).val());
        });
        
        if (selectedCodes.length === 0) {
            alert('لطفاً حداقل یک محصول را انتخاب کنید');
            addLogEntry('خطا: هیچ محصولی انتخاب نشده است', 'error');
            return;
        }
        
        // Check selected fields (از فیلدهای specific استفاده کنیم)
        selectedFields = [];
        $('.field-checkbox-specific:checked').each(function() {
            selectedFields.push($(this).val());
        });
        
        if (selectedFields.length === 0) {
            alert('لطفاً حداقل یک فیلد را برای ایمپورت انتخاب کنید');
            addLogEntry('خطا: هیچ فیلدی برای ایمپورت انتخاب نشده است', 'error');
            return;
        }
        
        if (!confirm(`آیا مطمئن هستید که می‌خواهید ${selectedCodes.length} محصول را با ${selectedFields.length} فیلد import کنید؟`)) {
            addLogEntry('عملیات ایمپورت توسط کاربر لغو شد', 'info');
            return;
        }
        
        isImporting = true;
        $('#specific-import').slideUp();
        $('#import-progress').slideDown();
        
        addLogEntry(`شروع ایمپورت ${selectedCodes.length} محصول انتخابی با ${selectedFields.length} فیلد...`, 'info');
        
        // AJAX request
        $.ajax({
            url: zargarAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'zargar_import_specific_products',
                nonce: zargarAjax.importNonce,
                codes: JSON.stringify(selectedCodes),
                fields: JSON.stringify(selectedFields)
            },
            timeout: 120000, // 2 minutes
            success: function(response) {
                if (response && response.success) {
                    addLogEntry('ایمپورت محصولات انتخابی با موفقیت شروع شد', 'success');
                    startProgressPolling();
                } else {
                    const errorMsg = (response && response.data && response.data.message) || 'خطای نامشخص';
                    addLogEntry('خطا در شروع ایمپورت: ' + errorMsg, 'error');
                    isImporting = false;
                    $('#import-progress').slideUp();
                    $('#specific-import').slideDown();
                }
            },
            error: function(xhr, status, error) {
                let errorMsg = 'خطا در ارتباط با سرور';
                if (status === 'timeout') errorMsg = 'زمان اتصال به پایان رسید';
                
                addLogEntry('خطای شبکه: ' + errorMsg, 'error');
                isImporting = false;
                $('#import-progress').slideUp();
                $('#specific-import').slideDown();
            }
        });
    }
    
    /**
     * نمایش خطا
     */
    function showError(message) {
        $('#import-stats').html(`
            <div class="error-message">
                <i class="lni lni-warning"></i>
                <p>${message}</p>
            </div>
        `);
    }
    
})(jQuery);
