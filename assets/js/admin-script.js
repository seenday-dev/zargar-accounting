/**
 * اسکریپت‌های مدیریت افزونه حسابداری زرگر
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        
        /**
         * تست اتصال به API
         */
        $('#test-connection').on('click', function(e) {
            e.preventDefault();
            const $btn = $(this);
            const originalText = $btn.html();
            
            $btn.prop('disabled', true)
                .html('<span class="zargar-loading"></span> در حال تست...');
            
            $.ajax({
                url: zargarAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'zargar_test_connection',
                    nonce: zargarAdmin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        showNotification('اتصال با موفقیت برقرار شد', 'success');
                    } else {
                        showNotification(response.data.message || 'خطا در برقراری اتصال', 'error');
                    }
                },
                error: function() {
                    showNotification('خطا در ارسال درخواست', 'error');
                },
                complete: function() {
                    $btn.prop('disabled', false).html(originalText);
                }
            });
        });

        /**
         * همگام‌سازی دستی
         */
        $('#sync-now').on('click', function(e) {
            e.preventDefault();
            const $btn = $(this);
            const originalText = $btn.html();
            
            if (!confirm('آیا از شروع همگام‌سازی اطمینان دارید؟')) {
                return;
            }
            
            $btn.prop('disabled', true)
                .html('<span class="zargar-loading"></span> در حال همگام‌سازی...');
            
            $.ajax({
                url: zargarAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'zargar_manual_sync',
                    nonce: zargarAdmin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        showNotification('همگام‌سازی با موفقیت شروع شد', 'success');
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    } else {
                        showNotification(response.data.message || 'خطا در همگام‌سازی', 'error');
                    }
                },
                error: function() {
                    showNotification('خطا در ارسال درخواست', 'error');
                },
                complete: function() {
                    $btn.prop('disabled', false).html(originalText);
                }
            });
        });

        /**
         * نمایش نوتیفیکیشن
         */
        function showNotification(message, type) {
            const $notification = $('<div class="zargar-notification zargar-notification-' + type + '">' +
                '<span class="notification-message">' + message + '</span>' +
                '<button class="notification-close">&times;</button>' +
                '</div>');
            
            $('body').append($notification);
            
            setTimeout(function() {
                $notification.addClass('show');
            }, 100);
            
            $notification.find('.notification-close').on('click', function() {
                $notification.removeClass('show');
                setTimeout(function() {
                    $notification.remove();
                }, 300);
            });
            
            setTimeout(function() {
                $notification.removeClass('show');
                setTimeout(function() {
                    $notification.remove();
                }, 300);
            }, 5000);
        }

        /**
         * حذف ردیف جدول
         */
        $(document).on('click', '.zargar-delete-row', function(e) {
            e.preventDefault();
            if (confirm('آیا از حذف این مورد اطمینان دارید؟')) {
                const $row = $(this).closest('tr');
                $row.fadeOut(function() {
                    $row.remove();
                });
            }
        });

        /**
         * انتخاب همه چک‌باکس‌ها
         */
        $('.zargar-select-all').on('change', function() {
            const isChecked = $(this).prop('checked');
            $(this).closest('table').find('tbody input[type="checkbox"]').prop('checked', isChecked);
        });

    });

})(jQuery);