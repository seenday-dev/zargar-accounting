@include('components.header', ['title' => 'تنظیمات'])

<div class="zargar-content-wrapper">
    <aside class="zargar-sidebar-wrapper">
        @include('components.sidebar')
    </aside>
    
    <main class="zargar-main-content">
        <div class="content-inner">
            <h2 class="page-title" style="font-size: 28px; color: var(--gold-400); margin-bottom: var(--space-xl);">تنظیمات سیستم</h2>
            
            <form method="post" class="settings-form">
                @nonce('zargar_settings')
                
                <!-- Connection Settings -->
                <div class="settings-section">
                    <h3 class="settings-section-title">تنظیمات اتصال</h3>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="server_ip" class="form-label">آدرس IP سرور</label>
                            <input 
                                type="text" 
                                id="server_ip" 
                                name="server_ip" 
                                value="{{ $server_ip ?? '' }}" 
                                class="form-control" 
                                placeholder="192.168.1.100"
                                data-tooltip="آدرس IP سرور حسابداری زرگر">
                            <p class="form-description">آدرس IP سرور مرکزی حسابداری</p>
                        </div>
                        
                        <div class="form-group">
                            <label for="server_port" class="form-label">پورت</label>
                            <input 
                                type="number" 
                                id="server_port" 
                                name="server_port" 
                                value="{{ $server_port ?? '8080' }}" 
                                class="form-control" 
                                placeholder="8080"
                                min="1" 
                                max="65535">
                            <p class="form-description">پورت ارتباطی با سرور</p>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="username" class="form-label">نام کاربری</label>
                            <input 
                                type="text" 
                                id="username" 
                                name="username" 
                                value="{{ $username ?? '' }}" 
                                class="form-control" 
                                placeholder="admin"
                                autocomplete="username">
                            <p class="form-description">نام کاربری برای احراز هویت</p>
                        </div>
                        
                        <div class="form-group">
                            <label for="password" class="form-label">رمز عبور</label>
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                value="{{ $password ?? '' }}" 
                                class="form-control" 
                                placeholder="••••••••"
                                autocomplete="current-password">
                            <p class="form-description">رمز عبور حساب کاربری</p>
                        </div>
                    </div>
                </div>
                
                <!-- Component Placeholder 1 -->
                <div class="settings-section component-placeholder">
                    <span class="component-placeholder-icon">📦</span>
                    <p class="component-placeholder-text">کامپوننت شماره ۱ - آماده برای توسعه</p>
                </div>
                
                <!-- Component Placeholder 2 -->
                <div class="settings-section component-placeholder">
                    <span class="component-placeholder-icon">🔧</span>
                    <p class="component-placeholder-text">کامپوننت شماره ۲ - آماده برای توسعه</p>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <span>ذخیره تنظیمات</span>
                    </button>
                    <button type="reset" class="btn btn-secondary">بازنشانی</button>
                    <button type="button" class="btn btn-secondary" onclick="testConnection()">تست اتصال</button>
                </div>
            </form>
        </div>
    </main>
</div>

@include('components.footer')

<script>
function testConnection() {
    const serverIp = document.getElementById('server_ip').value;
    const serverPort = document.getElementById('server_port').value;
    
    if (!serverIp || !serverPort) {
        alert('لطفاً آدرس IP و پورت را وارد کنید');
        return;
    }
    
    // Simulate connection test
    const btn = event.target;
    btn.innerHTML = '<span class="loading-spinner"></span> در حال تست...';
    btn.disabled = true;
    
    setTimeout(() => {
        alert('✓ اتصال موفقیت‌آمیز بود!\n\nآدرس: ' + serverIp + ':' + serverPort);
        btn.innerHTML = 'تست اتصال';
        btn.disabled = false;
    }, 2000);
}
</script>
