@include('components.header', ['title' => 'وضعیت همگام‌سازی'])

@include('components.navigation')

<div class="zargar-content-wrapper">
    <aside class="zargar-sidebar-wrapper">
        @include('components.sidebar')
    </aside>
    
    <main class="zargar-main-content">
        @include('partials.breadcrumb', [
            'breadcrumbs' => [
                ['title' => 'حسابداری زرگر', 'url' => '?page=zargar-accounting'],
                ['title' => 'همگام‌سازی']
            ]
        ])
        
        <div class="content-inner">
            <div class="sync-header">
                <h2>همگام‌سازی با سرور</h2>
                <button class="btn btn-primary" onclick="startSync()">
                    <span class="dashicons dashicons-update"></span>
                    شروع همگام‌سازی
                </button>
            </div>
            
            @include('partials.sync-progress', [
                'progress_title' => 'در حال همگام‌سازی داده‌ها...',
                'progress_percent' => $sync_progress ?? 0,
                'progress_message' => $sync_message ?? 'آماده برای شروع'
            ])
            
            <div class="sync-stats">
                <div class="stat-card">
                    <div class="stat-icon">
                        <span class="dashicons dashicons-upload"></span>
                    </div>
                    <div class="stat-content">
                        <h4>آخرین همگام‌سازی</h4>
                        <p class="stat-value">{{ $last_sync ?? 'هرگز' }}</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <span class="dashicons dashicons-yes-alt"></span>
                    </div>
                    <div class="stat-content">
                        <h4>موفقیت‌آمیز</h4>
                        <p class="stat-value">{{ $success_count ?? 0 }}</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <span class="dashicons dashicons-dismiss"></span>
                    </div>
                    <div class="stat-content">
                        <h4>ناموفق</h4>
                        <p class="stat-value">{{ $failed_count ?? 0 }}</p>
                    </div>
                </div>
            </div>
            
            <div class="sync-info">
                <h3>اطلاعات همگام‌سازی</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <strong>وضعیت اتصال:</strong>
                        <span class="connection-status status-connected">متصل</span>
                    </div>
                    <div class="info-item">
                        <strong>نوع همگام‌سازی:</strong>
                        <span>خودکار</span>
                    </div>
                    <div class="info-item">
                        <strong>فاصله زمانی:</strong>
                        <span>هر 30 دقیقه</span>
                    </div>
                    <div class="info-item">
                        <strong>آخرین خطا:</strong>
                        <span>{{ $last_error ?? 'ندارد' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

@include('components.footer')

<style>
    .sync-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }
    
    .sync-header h2 {
        font-size: 22px;
        color: #495057;
    }
    
    .sync-header .btn {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .sync-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin: 30px 0;
    }
    
    .stat-card {
        background: white;
        padding: 20px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .stat-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 28px;
        margin-left: 15px;
    }
    
    .stat-content h4 {
        font-size: 13px;
        color: #6c757d;
        margin-bottom: 5px;
        font-weight: normal;
    }
    
    .stat-value {
        font-size: 22px;
        font-weight: bold;
        color: #495057;
    }
    
    .sync-info {
        background: white;
        padding: 25px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .sync-info h3 {
        font-size: 18px;
        color: #495057;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #667eea;
    }
    
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 15px;
    }
    
    .info-item {
        padding: 12px 0;
        font-size: 14px;
    }
    
    .info-item strong {
        display: block;
        margin-bottom: 5px;
        color: #6c757d;
    }
    
    .connection-status {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: bold;
    }
    
    .status-connected {
        background: #d4edda;
        color: #155724;
    }
</style>

<script>
function startSync() {
    alert('همگام‌سازی شروع شد. این یک نمونه است.');
}
</script>
