@include('components.header', ['title' => 'گزارش‌های سیستم'])

@include('components.navigation')

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
                <h2>گزارش‌های سیستم</h2>
                <div class="logs-filters">
                    <select id="log-level-filter">
                        <option value="">همه سطوح</option>
                        <option value="ERROR">خطا</option>
                        <option value="WARNING">هشدار</option>
                        <option value="INFO">اطلاعات</option>
                        <option value="DEBUG">دیباگ</option>
                    </select>
                    <button class="btn btn-secondary" onclick="refreshLogs()">بروزرسانی</button>
                </div>
            </div>
            
            <div class="logs-container">
                <table class="logs-table">
                    <thead>
                        <tr>
                            <th>زمان</th>
                            <th>سطح</th>
                            <th>کاربر</th>
                            <th>پیام</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($logs) && !empty($logs))
                            @foreach($logs as $log)
                                <tr class="log-row log-{{ strtolower($log['level'] ?? 'info') }}">
                                    <td>{{ $log['time'] ?? '-' }}</td>
                                    <td><span class="log-badge log-badge-{{ strtolower($log['level'] ?? 'info') }}">{{ $log['level'] ?? 'INFO' }}</span></td>
                                    <td>{{ $log['user'] ?? '-' }}</td>
                                    <td>{{ $log['message'] ?? '-' }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4" style="text-align: center; padding: 40px; color: #6c757d;">
                                    <span class="dashicons dashicons-info" style="font-size: 48px; opacity: 0.3;"></span>
                                    <p>هیچ گزارشی یافت نشد</p>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

@include('components.footer')

<style>
    .logs-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    
    .logs-header h2 {
        font-size: 22px;
        color: #495057;
    }
    
    .logs-filters {
        display: flex;
        gap: 10px;
    }
    
    .logs-filters select {
        padding: 8px 12px;
        border: 1px solid #ced4da;
        border-radius: 4px;
        font-size: 14px;
    }
    
    .btn {
        padding: 8px 16px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.3s;
    }
    
    .btn-secondary {
        background: #6c757d;
        color: white;
    }
    
    .btn-secondary:hover {
        background: #5a6268;
    }
    
    .logs-container {
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .logs-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .logs-table thead {
        background: #f8f9fa;
    }
    
    .logs-table th {
        padding: 15px;
        text-align: right;
        font-weight: 600;
        color: #495057;
        border-bottom: 2px solid #dee2e6;
    }
    
    .logs-table td {
        padding: 12px 15px;
        border-bottom: 1px solid #f8f9fa;
        font-size: 13px;
    }
    
    .logs-table tbody tr:hover {
        background: #f8f9fa;
    }
    
    .log-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: bold;
        text-transform: uppercase;
    }
    
    .log-badge-error {
        background: #f8d7da;
        color: #721c24;
    }
    
    .log-badge-warning {
        background: #fff3cd;
        color: #856404;
    }
    
    .log-badge-info {
        background: #d1ecf1;
        color: #0c5460;
    }
    
    .log-badge-debug {
        background: #e2e3e5;
        color: #383d41;
    }
</style>

<script>
function refreshLogs() {
    location.reload();
}
</script>
