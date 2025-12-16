@include('components.header', ['title' => 'گزارش‌های سیستم'])

<style>
    @import url('https://cdn.jsdelivr.net/npm/yekan-font@1.0.0/css/yekan-font.min.css');
    * {
        font-family: 'yekan', 'Tahoma', 'Arial', sans-serif !important;
    }
</style>

<div class="zargar-content-wrapper">
    <aside class="zargar-sidebar-wrapper">
        @include('components.sidebar')
    </aside>
    
    <main class="zargar-main-content">
        @include('partials.breadcrumb', [
            'breadcrumbs' => [
                ['title' => 'حسابداری زرگر', 'url' => '?page=zargar-accounting'],
                ['title' => 'گزارش‌ها']
            ]
        ])
        
        <div class="content-inner">
            <div class="logs-header">
                <h2 class="logs-title">گزارش‌های سیستم</h2>
                <div class="logs-filters">
                    <select id="log-level-filter" class="log-filter-select">
                        <option value="all">همه سطوح</option>
                        <option value="INFO">اطلاع</option>
                        <option value="SUCCESS">موفق</option>
                        <option value="WARNING">هشدار</option>
                        <option value="ERROR">خطا</option>
                    </select>
                    <button id="refresh-logs" class="log-btn log-btn-secondary">
                        <i class="fas fa-sync-alt"></i> بروزرسانی
                    </button>
                    <button id="clear-logs" class="log-btn log-btn-danger">
                        <i class="fas fa-trash"></i> حذف
                    </button>
                </div>
            </div>
            
            <div class="log-tabs">
                <button class="log-tab active" data-type="product">
                    <span class="tab-icon">📦</span>
                    <span class="tab-label">محصولات</span>
                    <span class="tab-badge">0</span>
                </button>
                <button class="log-tab" data-type="sales">
                    <span class="tab-icon">💰</span>
                    <span class="tab-label">فروش</span>
                    <span class="tab-badge">0</span>
                </button>
                <button class="log-tab" data-type="price">
                    <span class="tab-icon">💵</span>
                    <span class="tab-label">قیمت</span>
                    <span class="tab-badge">0</span>
                </button>
                <button class="log-tab" data-type="error">
                    <span class="tab-icon">⚠️</span>
                    <span class="tab-label">خطاها</span>
                    <span class="tab-badge">0</span>
                </button>
            </div>
            
            <div class="logs-container" id="logs-container">
                <div class="log-loading">
                    <div class="log-loading-spinner"></div>
                    <p>در حال بارگذاری...</p>
                </div>
            </div>
        </div>
    </main>
</div>

<style>
.log-context-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 10000;
}

.log-context-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
}

.log-context-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: var(--color-surface);
    border: 1px solid var(--color-border);
    border-radius: var(--radius-md);
    width: 90%;
    max-width: 600px;
    max-height: 80vh;
    display: flex;
    flex-direction: column;
}

.log-context-header {
    padding: var(--space-md);
    border-bottom: 1px solid var(--color-border);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.log-context-header h3 {
    margin: 0;
    font-size: var(--font-size-md);
    font-weight: 600;
}

.log-context-close {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    padding: 0;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--color-text-secondary);
    transition: var(--transition-fast);
}

.log-context-close:hover {
    color: var(--color-text);
}

.log-context-body {
    padding: var(--space-md);
    overflow-y: auto;
}

.log-context-body pre {
    background: var(--color-bg);
    padding: var(--space-sm);
    border-radius: var(--radius-sm);
    font-size: var(--font-size-xs);
    line-height: 1.6;
    margin: 0;
    overflow-x: auto;
    direction: ltr;
    text-align: left;
}

.btn-view-context {
    background: none;
    border: 1px solid var(--color-border);
    padding: 4px 8px;
    border-radius: var(--radius-sm);
    font-size: var(--font-size-xs);
    cursor: pointer;
    color: var(--color-text-secondary);
    transition: var(--transition-fast);
}

.btn-view-context:hover {
    background: var(--color-bg);
    color: var(--color-text);
}

.logs-filters {
    display: flex;
    gap: var(--space-sm);
    align-items: center;
}

.log-filter-select {
    padding: 6px 12px;
    border: 1px solid var(--color-border);
    border-radius: var(--radius-sm);
    background: var(--color-surface);
    font-size: var(--font-size-sm);
    color: var(--color-text);
    cursor: pointer;
}

.log-btn {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 6px 12px;
    border: none;
    border-radius: var(--radius-sm);
    font-size: var(--font-size-sm);
    cursor: pointer;
    transition: var(--transition-fast);
}

.log-btn i {
    width: 16px;
    height: 16px;
    font-size: 16px;
}

.log-btn-secondary {
    background: var(--color-surface);
    color: var(--color-text);
    border: 1px solid var(--color-border);
}

.log-btn-secondary:hover {
    background: var(--color-bg);
}

.log-btn-danger {
    background: var(--color-error);
    color: white;
}

.log-btn-danger:hover {
    background: #c0392b;
}
</style>

@include('components.footer')
