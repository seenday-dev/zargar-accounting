<aside class="zargar-sidebar">
    <ul class="sidebar-menu">
        <li class="sidebar-menu-item {{ (isset($_GET['page']) && $_GET['page'] === 'zargar-accounting') ? 'active' : '' }}">
            <a href="?page=zargar-accounting" class="sidebar-menu-link">
                <span class="sidebar-menu-icon">
                    <i class="lni lni-home"></i>
                </span>
                <span class="sidebar-menu-text">داشبورد</span>
            </a>
        </li>
        
        <li class="sidebar-menu-item {{ (isset($_GET['page']) && $_GET['page'] === 'zargar-accounting-settings') ? 'active' : '' }}">
            <a href="?page=zargar-accounting-settings" class="sidebar-menu-link">
                <span class="sidebar-menu-icon">
                    <i class="lni lni-cog"></i>
                </span>
                <span class="sidebar-menu-text">تنظیمات</span>
            </a>
        </li>
        
        <li class="sidebar-menu-item {{ (isset($_GET['page']) && $_GET['page'] === 'zargar-accounting-logs') ? 'active' : '' }}">
            <a href="?page=zargar-accounting-logs" class="sidebar-menu-link">
                <span class="sidebar-menu-icon">
                    <i class="lni lni-list"></i>
                </span>
                <span class="sidebar-menu-text">گزارش‌ها</span>
            </a>
        </li>
    </ul>
    
    <div class="sidebar-divider"></div>
    
    <div class="sidebar-info">
        <div class="sidebar-info-title">وضعیت سیستم</div>
        <div class="sidebar-info-content">
            <div class="status-indicator">فعال</div>
            <p style="margin-top: 12px; font-size: 12px;">نسخه {{ $plugin_version }}</p>
        </div>
    </div>
</aside>
