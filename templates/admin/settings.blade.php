@include('components.header', ['title' => 'تنظیمات'])

<div class="zargar-content-wrapper">
    <aside class="zargar-sidebar-wrapper">
        @include('components.sidebar')
    </aside>
    
    <main class="zargar-main-content">
        <div class="content-inner">
            <h2 class="page-title" style="font-size: 28px; color: var(--gold-400); margin-bottom: var(--space-xl);">تنظیمات سیستم</h2>
            
            @if(isset($_GET['updated']) && $_GET['updated'] === 'true')
            <div class="settings-notice success">
                <i class="lni lni-checkmark-circle"></i>
                تنظیمات با موفقیت ذخیره شد
            </div>
            @endif
            
            <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" class="settings-form" id="settings-form">
                <input type="hidden" name="action" value="zargar_save_settings">
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
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="lni lni-save"></i>
                        ذخیره تنظیمات
                    </button>
                    <button type="reset" class="btn btn-secondary">
                        <i class="lni lni-reload"></i>
                        بازنشانی
                    </button>
                    <button type="button" class="btn btn-info" id="test-connection">
                        <i class="lni lni-pulse"></i>
                        تست اتصال
                    </button>
                </div>
            </form>
            
            <!-- Connection Test Result -->
            <div class="connection-result" id="connection-result" style="display: none;"></div>
        </div>
    </main>
</div>

@include('components.footer')
