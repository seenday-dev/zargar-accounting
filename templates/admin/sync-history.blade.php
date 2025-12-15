@include('components.header', ['title' => 'تاریخچه همگام‌سازی'])

@include('components.navigation')

<div class="zargar-content-wrapper">
    <aside class="zargar-sidebar-wrapper">
        @include('components.sidebar')
    </aside>
    
    <main class="zargar-main-content">
        @include('partials.breadcrumb', [
            'breadcrumbs' => [
                ['title' => 'حسابداری زرگر', 'url' => '?page=zargar-accounting'],
                ['title' => 'تاریخچه همگام‌سازی']
            ]
        ])
        
        <div class="content-inner">
            <h2>تاریخچه همگام‌سازی</h2>
            
            <div class="history-container">
                <table class="history-table">
                    <thead>
                        <tr>
                            <th>تاریخ</th>
                            <th>زمان</th>
                            <th>نوع</th>
                            <th>وضعیت</th>
                            <th>تعداد</th>
                            <th>مدت زمان</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($history) && !empty($history))
                            @foreach($history as $item)
                                <tr>
                                    <td>{{ $item['date'] ?? '-' }}</td>
                                    <td>{{ $item['time'] ?? '-' }}</td>
                                    <td>{{ $item['type'] ?? '-' }}</td>
                                    <td>
                                        <span class="status-badge status-{{ $item['status'] ?? 'pending' }}">
                                            {{ $item['status_label'] ?? 'در انتظار' }}
                                        </span>
                                    </td>
                                    <td>{{ $item['count'] ?? 0 }}</td>
                                    <td>{{ $item['duration'] ?? '-' }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 40px; color: #6c757d;">
                                    <span class="dashicons dashicons-update" style="font-size: 48px; opacity: 0.3;"></span>
                                    <p>هیچ تاریخچه‌ای یافت نشد</p>
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
    .history-container {
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        margin-top: 20px;
    }
    
    .history-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .history-table thead {
        background: #f8f9fa;
    }
    
    .history-table th {
        padding: 15px;
        text-align: right;
        font-weight: 600;
        color: #495057;
        border-bottom: 2px solid #dee2e6;
    }
    
    .history-table td {
        padding: 12px 15px;
        border-bottom: 1px solid #f8f9fa;
        font-size: 13px;
    }
    
    .history-table tbody tr:hover {
        background: #f8f9fa;
    }
    
    .status-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: bold;
    }
    
    .status-success {
        background: #d4edda;
        color: #155724;
    }
    
    .status-failed {
        background: #f8d7da;
        color: #721c24;
    }
    
    .status-pending {
        background: #fff3cd;
        color: #856404;
    }
</style>
