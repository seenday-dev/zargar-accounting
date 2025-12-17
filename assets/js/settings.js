/**
 * Zargar Accounting - Settings Page
 * تست اتصال به سرور حسابداری
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        
        console.log('Settings JS loaded');
        console.log('zargarAjax:', typeof zargarAjax !== 'undefined' ? zargarAjax : 'NOT DEFINED');
        console.log('jQuery version:', $.fn.jquery);
        
        // Test Connection Button
        $('#test-connection').on('click', function(e) {
            e.preventDefault();
            
            const btn = $(this);
            const resultDiv = $('#connection-result');
            
            // Get values
            const server_ip = $('#server_ip').val().trim();
            const server_port = $('#server_port').val().trim();
            const username = $('#username').val().trim();
            const password = $('#password').val().trim();
            
            console.log('Form values:', { server_ip, server_port, username });
            
            // Validate
            if (!server_ip || !server_port) {
                resultDiv.html(`
                    <div class="connection-error">
                        <div class="connection-icon"><i class="lni lni-warning"></i></div>
                        <h3>اطلاعات ناقص</h3>
                        <p>لطفاً آدرس IP و پورت را وارد کنید</p>
                    </div>
                `).fadeIn();
                return;
            }
            
            // Check if zargarAjax is defined
            if (typeof zargarAjax === 'undefined') {
                console.error('zargarAjax is not defined! Check wp_localize_script');
                resultDiv.html(`
                    <div class="connection-error">
                        <div class="connection-icon"><i class="lni lni-close"></i></div>
                        <h3>خطای برنامه‌نویسی</h3>
                        <p>متغیر zargarAjax تعریف نشده است. لطفاً کش را پاک کنید.</p>
                    </div>
                `).fadeIn();
                return;
            }
            
            // Disable button
            btn.prop('disabled', true);
            btn.html('<span class="loading-spinner"></span> در حال تست...');
            
            // Clear previous result
            resultDiv.hide().html('');
            
            const ajaxData = {
                action: 'zargar_test_connection',
                nonce: zargarAjax.testConnectionNonce,
                server_ip: server_ip,
                server_port: server_port,
                username: username,
                password: password
            };
            
            console.log('Sending AJAX to:', zargarAjax.ajaxurl);
            console.log('Data:', ajaxData);
            
            // Send AJAX request
            $.ajax({
                url: zargarAjax.ajaxurl,
                type: 'POST',
                dataType: 'json',
                data: ajaxData,
                timeout: 30000,
                success: function(response) {
                    console.log('AJAX Success:', response);
                    
                    if (response.success) {
                        resultDiv.html(`
                            <div class="connection-success">
                                <div class="connection-icon">
                                    <i class="lni lni-checkmark-circle"></i>
                                </div>
                                <h3>اتصال موفقیت‌آمیز</h3>
                                <p>${response.data.message}</p>
                                <div class="connection-details">
                                    <div class="detail-item">
                                        <span class="detail-label">سرور:</span>
                                        <span class="detail-value">${response.data.server}</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">نام کاربری:</span>
                                        <span class="detail-value">${response.data.username}</span>
                                    </div>
                                    ${response.data.fullname ? `
                                    <div class="detail-item">
                                        <span class="detail-label">نام کامل:</span>
                                        <span class="detail-value">${response.data.fullname}</span>
                                    </div>
                                    ` : ''}
                                    <div class="detail-item">
                                        <span class="detail-label">UserKey:</span>
                                        <span class="detail-value code">${response.data.userkey.substring(0, 30)}...</span>
                                    </div>
                                    ${response.data.version ? `
                                    <div class="detail-item">
                                        <span class="detail-label">نسخه API:</span>
                                        <span class="detail-value">${response.data.version}</span>
                                    </div>
                                    ` : ''}
                                </div>
                            </div>
                        `).fadeIn();
                    } else {
                        resultDiv.html(`
                            <div class="connection-error">
                                <div class="connection-icon">
                                    <i class="lni lni-close"></i>
                                </div>
                                <h3>خطا در اتصال</h3>
                                <p>${response.data.message}</p>
                                <div class="error-suggestions">
                                    <h4>راهنمایی:</h4>
                                    <ul>
                                        <li>آدرس IP و پورت سرور را بررسی کنید</li>
                                        <li>نام کاربری و رمز عبور را چک کنید</li>
                                        <li>اطمینان حاصل کنید سرور روشن و در دسترس است</li>
                                        <li>فایروال یا آنتی‌ویروس ممکن است اتصال را مسدود کند</li>
                                    </ul>
                                </div>
                            </div>
                        `).fadeIn();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', { xhr, status, error });
                    console.error('Response Text:', xhr.responseText);
                    
                    let errorMsg = 'خطا در ارتباط با سرور WordPress';
                    
                    // Try to get more specific error
                    if (xhr.status === 0) {
                        errorMsg = 'خطا در ارسال درخواست - احتمالاً مشکل شبکه';
                    } else if (xhr.status === 403) {
                        errorMsg = 'دسترسی رد شد - مشکل امنیتی';
                    } else if (xhr.status === 500) {
                        errorMsg = 'خطای سرور WordPress';
                    }
                    
                    resultDiv.html(`
                        <div class="connection-error">
                            <div class="connection-icon">
                                <i class="lni lni-close"></i>
                            </div>
                            <h3>خطا در ارتباط</h3>
                            <p>${errorMsg}</p>
                            <div class="error-details">
                                <p><strong>کد خطا:</strong> ${xhr.status || 'نامشخص'}</p>
                                <p><strong>وضعیت:</strong> ${status}</p>
                            </div>
                        </div>
                    `).fadeIn();
                },
                complete: function() {
                    // Re-enable button
                    btn.prop('disabled', false);
                    btn.html('<i class="lni lni-pulse"></i> تست اتصال');
                }
            });
        });
        
    });
    
})(jQuery);
