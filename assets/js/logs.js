/**
 * Advanced Logs Page - AJAX Tab Switching
 * 
 * @package ZargarAccounting
 * @since 1.0.0
 */

(function($) {
    'use strict';
    
    const LogsManager = {
        currentType: 'product',
        currentLevel: 'all',
        isLoading: false,
        
        init() {
            this.bindEvents();
            this.loadLogs('product'); // Load default tab
        },
        
        bindEvents() {
            // Tab switching
            $('.log-tab').on('click', (e) => {
                e.preventDefault();
                const type = $(e.currentTarget).data('type');
                this.switchTab(type);
            });
            
            // Level filter
            $('#log-level-filter').on('change', (e) => {
                this.currentLevel = $(e.currentTarget).val();
                this.loadLogs(this.currentType);
            });
            
            // Refresh button
            $('#refresh-logs').on('click', (e) => {
                e.preventDefault();
                this.loadLogs(this.currentType);
            });
            
            // Clear logs
            $('#clear-logs').on('click', (e) => {
                e.preventDefault();
                this.clearLogs();
            });
        },
        
        switchTab(type) {
            if (this.isLoading || type === this.currentType) {
                return;
            }
            
            // Update tab UI
            $('.log-tab').removeClass('active');
            $(`.log-tab[data-type="${type}"]`).addClass('active');
            
            // Update current type
            this.currentType = type;
            
            // Load logs
            this.loadLogs(type);
        },
        
        loadLogs(type) {
            if (this.isLoading) {
                return;
            }
            
            console.log('Loading logs for type:', type);
            console.log('AJAX URL:', zargarLogs.ajaxurl);
            console.log('Nonce:', zargarLogs.nonce);
            
            this.isLoading = true;
            this.showLoading();
            
            $.ajax({
                url: zargarLogs.ajaxurl,
                type: 'POST',
                data: {
                    action: 'zargar_get_logs',
                    nonce: zargarLogs.nonce,
                    type: type,
                    level: this.currentLevel === 'all' ? null : this.currentLevel,
                    limit: 100
                },
                success: (response) => {
                    console.log('AJAX Response:', response);
                    if (response.success) {
                        console.log('Logs received:', response.data.logs);
                        this.renderLogs(response.data.logs);
                        this.updateStats(response.data.stats);
                    } else {
                        console.error('Error response:', response.data);
                        this.showError(response.data.message || 'خطا در دریافت لاگ‌ها');
                    }
                },
                error: (xhr, status, error) => {
                    console.error('AJAX Error:', {xhr, status, error});
                    console.error('Response Text:', xhr.responseText);
                    this.showError('خطا در ارتباط با سرور: ' + error + ' - ' + xhr.responseText);
                },
                complete: () => {
                    console.log('AJAX Complete');
                    this.isLoading = false;
                    this.hideLoading();
                }
            });
        },
        
        renderLogs(logs) {
            const container = $('#logs-container');
            container.empty();
            
            if (!logs || logs.length === 0) {
                container.html(this.getEmptyState());
                return;
            }
            
            const table = $('<table>').addClass('logs-table');
            
            // Header
            const thead = $('<thead>').html(`
                <tr>
                    <th>زمان</th>
                    <th>سطح</th>
                    <th>کاربر</th>
                    <th>پیام</th>
                    <th>جزئیات</th>
                </tr>
            `);
            
            // Body
            const tbody = $('<tbody>');
            logs.forEach(log => {
                const row = $('<tr>').html(`
                    <td class="log-timestamp">${log.time || '-'}</td>
                    <td>
                        <span class="log-badge log-badge-${log.level.toLowerCase()}">
                            ${this.translateLevel(log.level)}
                        </span>
                    </td>
                    <td class="log-user">${log.user || 'guest'}</td>
                    <td class="log-message">${this.escapeHtml(log.message || '')}</td>
                    <td class="log-context">
                        ${log.context ? `<button class="btn-view-context" data-context='${this.escapeHtml(log.context)}'>مشاهده</button>` : '-'}
                    </td>
                `);
                tbody.append(row);
            });
            
            table.append(thead).append(tbody);
            container.html(table);
            
            // Bind context view
            $('.btn-view-context').on('click', (e) => {
                const context = $(e.currentTarget).data('context');
                this.showContextModal(context);
            });
        },
        
        updateStats(stats) {
            $('.log-tab').each(function() {
                const type = $(this).data('type');
                const badge = $(this).find('.tab-badge');
                
                if (stats[type]) {
                    badge.text(stats[type].total).show();
                } else {
                    badge.hide();
                }
            });
        },
        
        showLoading() {
            $('#logs-container').html(`
                <div class="log-loading">
                    <div class="log-loading-spinner"></div>
                    <p>در حال بارگذاری...</p>
                </div>
            `);
        },
        
        hideLoading() {
            // Loading is replaced by content
        },
        
        showError(message) {
            $('#logs-container').html(`
                <div class="empty-state">
                    <div class="empty-icon">⚠️</div>
                    <h3>خطا</h3>
                    <p>${this.escapeHtml(message)}</p>
                </div>
            `);
        },
        
        getEmptyState() {
            const titles = {
                product: 'محصولات',
                sales: 'فروش',
                price: 'قیمت',
                error: 'خطاها'
            };
            
            return `
                <div class="empty-state">
                    <div class="empty-icon">📋</div>
                    <h3>لاگی موجود نیست</h3>
                    <p>هیچ لاگ ${titles[this.currentType]} ثبت نشده است.</p>
                </div>
            `;
        },
        
        showContextModal(context) {
            const modal = $(`
                <div class="log-context-modal" id="context-modal">
                    <div class="log-context-overlay"></div>
                    <div class="log-context-content">
                        <div class="log-context-header">
                            <h3>جزئیات لاگ</h3>
                            <button class="log-context-close">&times;</button>
                        </div>
                        <div class="log-context-body">
                            <pre>${this.escapeHtml(this.formatJSON(context))}</pre>
                        </div>
                    </div>
                </div>
            `);
            
            $('body').append(modal);
            
            modal.find('.log-context-close, .log-context-overlay').on('click', () => {
                modal.fadeOut(200, () => modal.remove());
            });
            
            modal.fadeIn(200);
        },
        
        clearLogs() {
            if (!confirm('آیا از حذف تمام لاگ‌های این نوع اطمینان دارید؟')) {
                return;
            }
            
            $.ajax({
                url: zargarLogs.ajaxurl,
                type: 'POST',
                data: {
                    action: 'zargar_clear_logs',
                    nonce: zargarLogs.nonce,
                    type: this.currentType
                },
                success: (response) => {
                    if (response.success) {
                        this.loadLogs(this.currentType);
                        alert('لاگ‌ها با موفقیت حذف شدند.');
                    } else {
                        alert(response.data.message || 'خطا در حذف لاگ‌ها');
                    }
                },
                error: () => {
                    alert('خطا در ارتباط با سرور');
                }
            });
        },
        
        formatTimestamp(timestamp) {
            const date = new Date(timestamp.replace(' ', 'T'));
            const now = new Date();
            const diff = Math.floor((now - date) / 1000);
            
            if (diff < 60) return 'همین الان';
            if (diff < 3600) return Math.floor(diff / 60) + ' دقیقه پیش';
            if (diff < 86400) return Math.floor(diff / 3600) + ' ساعت پیش';
            
            return timestamp;
        },
        
        translateLevel(level) {
            const translations = {
                'INFO': 'اطلاع',
                'SUCCESS': 'موفق',
                'WARNING': 'هشدار',
                'ERROR': 'خطا'
            };
            return translations[level] || level;
        },
        
        formatJSON(jsonString) {
            try {
                const obj = JSON.parse(jsonString);
                return JSON.stringify(obj, null, 2);
            } catch (e) {
                return jsonString;
            }
        },
        
        escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    };
    
    // Initialize on document ready
    $(document).ready(() => {
        if ($('.log-tabs').length) {
            LogsManager.init();
        }
    });
    
})(jQuery);
