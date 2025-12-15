<aside class="zargar-sidebar">
    <div class="sidebar-section">
        <h3>منوی سریع</h3>
        <ul>
            <li><a href="?page=zargar-accounting">داشبورد</a></li>
            <li><a href="?page=zargar-accounting-sync">همگام‌سازی</a></li>
            <li><a href="?page=zargar-accounting-logs">گزارش‌ها</a></li>
        </ul>
    </div>
    
    <div class="sidebar-section">
        <h3>اطلاعات</h3>
        <div class="info-item">
            <strong>وضعیت:</strong>
            <span class="status-badge status-active">فعال</span>
        </div>
        <div class="info-item">
            <strong>نسخه:</strong>
            <span>{{ $plugin_version }}</span>
        </div>
    </div>
</aside>

<style>
    .zargar-sidebar {
        background: #f8f9fa;
        padding: 20px;
        min-height: 400px;
        border-left: 1px solid #e9ecef;
    }
    
    .sidebar-section {
        margin-bottom: 25px;
        padding-bottom: 20px;
        border-bottom: 1px solid #dee2e6;
    }
    
    .sidebar-section:last-child {
        border-bottom: none;
    }
    
    .sidebar-section h3 {
        font-size: 16px;
        color: #495057;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #667eea;
    }
    
    .sidebar-section ul {
        list-style: none;
    }
    
    .sidebar-section li {
        margin-bottom: 8px;
    }
    
    .sidebar-section a {
        color: #495057;
        text-decoration: none;
        display: block;
        padding: 8px 12px;
        border-radius: 4px;
        transition: all 0.3s;
    }
    
    .sidebar-section a:hover {
        background: #e9ecef;
        color: #667eea;
        padding-right: 18px;
    }
    
    .info-item {
        margin-bottom: 10px;
        font-size: 13px;
    }
    
    .info-item strong {
        display: inline-block;
        width: 70px;
        color: #6c757d;
    }
    
    .status-badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: bold;
    }
    
    .status-active {
        background: #d4edda;
        color: #155724;
    }
</style>
