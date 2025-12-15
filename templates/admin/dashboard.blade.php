@include('components.header', ['title' => 'داشبورد'])

@include('components.navigation')

<div class="zargar-content-wrapper">
    <aside class="zargar-sidebar-wrapper">
        @include('components.sidebar')
    </aside>
    
    <main class="zargar-main-content">
        @include('partials.breadcrumb', [
            'breadcrumbs' => [
                ['title' => 'حسابداری زرگر'],
                ['title' => 'داشبورد']
            ]
        ])
        
        <div class="content-inner">
            <div class="dashboard-widgets">
                <div class="widget widget-primary">
                    <div class="widget-icon">
                        <span class="dashicons dashicons-chart-line"></span>
                    </div>
                    <div class="widget-content">
                        <h3>گزارش‌ها</h3>
                        <p class="widget-number">1,234</p>
                        <p class="widget-label">تراکنش امروز</p>
                    </div>
                </div>
                
                <div class="widget widget-success">
                    <div class="widget-icon">
                        <span class="dashicons dashicons-yes-alt"></span>
                    </div>
                    <div class="widget-content">
                        <h3>موفق</h3>
                        <p class="widget-number">98.5%</p>
                        <p class="widget-label">نرخ موفقیت</p>
                    </div>
                </div>
                
                <div class="widget widget-warning">
                    <div class="widget-icon">
                        <span class="dashicons dashicons-update"></span>
                    </div>
                    <div class="widget-content">
                        <h3>همگام‌سازی</h3>
                        <p class="widget-number">15</p>
                        <p class="widget-label">دقیقه پیش</p>
                    </div>
                </div>
            </div>
            
            <div class="dashboard-section">
                <h2>خوش آمدید به حسابداری زرگر</h2>
                <p>این پنل مدیریت سیستم حسابداری زرگر است. از منوی بالا می‌توانید به بخش‌های مختلف دسترسی داشته باشید.</p>
            </div>
        </div>
    </main>
</div>

@include('components.footer')

<style>
    .zargar-content-wrapper {
        display: flex;
        min-height: 500px;
    }
    
    .zargar-sidebar-wrapper {
        width: 260px;
        flex-shrink: 0;
    }
    
    .zargar-main-content {
        flex: 1;
    }
    
    .content-inner {
        padding: 30px;
    }
    
    .dashboard-widgets {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .widget {
        background: white;
        border-radius: 8px;
        padding: 25px;
        display: flex;
        align-items: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transition: transform 0.3s;
    }
    
    .widget:hover {
        transform: translateY(-5px);
    }
    
    .widget-icon {
        width: 70px;
        height: 70px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        margin-left: 20px;
    }
    
    .widget-primary .widget-icon {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .widget-success .widget-icon {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: white;
    }
    
    .widget-warning .widget-icon {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
    }
    
    .widget-content h3 {
        font-size: 14px;
        color: #6c757d;
        margin-bottom: 5px;
        font-weight: normal;
    }
    
    .widget-number {
        font-size: 28px;
        font-weight: bold;
        color: #495057;
        margin: 5px 0;
    }
    
    .widget-label {
        font-size: 12px;
        color: #6c757d;
    }
    
    .dashboard-section {
        background: white;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .dashboard-section h2 {
        font-size: 22px;
        color: #495057;
        margin-bottom: 15px;
    }
    
    .dashboard-section p {
        color: #6c757d;
        line-height: 1.8;
    }
</style>
