@include('components.header', ['title' => 'داشبورد'])

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
                <div class="widget">
                    <div class="widget-content">
                        <div class="widget-icon">
                            <i class="lni lni-stats-up"></i>
                        </div>
                        <div class="widget-info">
                            <div class="widget-label">تراکنش امروز</div>
                            <div class="widget-value">1,234</div>
                            <div class="widget-change positive">↑ 12.5%</div>
                        </div>
                    </div>
                </div>
                
                <div class="widget">
                    <div class="widget-content">
                        <div class="widget-icon">
                            <i class="lni lni-checkmark-circle"></i>
                        </div>
                        <div class="widget-info">
                            <div class="widget-label">نرخ موفقیت</div>
                            <div class="widget-value">98.5%</div>
                            <div class="widget-change positive">↑ 2.1%</div>
                        </div>
                    </div>
                </div>
                
                <div class="widget">
                    <div class="widget-content">
                        <div class="widget-icon">
                            <i class="lni lni-reload"></i>
                        </div>
                        <div class="widget-info">
                            <div class="widget-label">آخرین همگام‌سازی</div>
                            <div class="widget-value">15</div>
                            <div class="widget-change">دقیقه پیش</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <h2 class="dashboard-card-title">خوش آمدید به حسابداری زرگر</h2>
                    <a href="?page=zargar-accounting-settings" class="dashboard-card-action">تنظیمات</a>
                </div>
                <div class="dashboard-card-content">
                    <p>این پنل مدیریت سیستم حسابداری زرگر است. سیستمی حرفه‌ای برای مدیریت حسابداری طلافروشی‌ها با تکنولوژی روز.</p>
                    <p style="margin-top: 12px;">از منوی کناری می‌توانید به بخش‌های مختلف دسترسی داشته باشید.</p>
                </div>
            </div>
        </div>
    </main>
</div>

@include('components.footer')
