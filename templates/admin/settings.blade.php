@include('components.header', ['title' => 'تنظیمات'])

@include('components.navigation')

<div class="zargar-content-wrapper">
    <aside class="zargar-sidebar-wrapper">
        @include('components.sidebar')
    </aside>
    
    <main class="zargar-main-content">
        @include('partials.breadcrumb', [
            'breadcrumbs' => [
                ['title' => 'حسابداری زرگر', 'url' => '?page=zargar-accounting'],
                ['title' => 'تنظیمات']
            ]
        ])
        
        <div class="content-inner">
            <h2>تنظیمات سیستم</h2>
            
            <form method="post" class="settings-form">
                @nonce('zargar_settings')
                
                <div class="settings-section">
                    <h3>تنظیمات عمومی</h3>
                    
                    <div class="form-group">
                        <label for="api_url">آدرس API</label>
                        <input type="url" id="api_url" name="api_url" value="{{ $api_url ?? '' }}" class="form-control" placeholder="https://api.example.com">
                        <p class="form-description">آدرس سرور API حسابداری زرگر</p>
                    </div>
                    
                    <div class="form-group">
                        <label for="api_key">کلید API</label>
                        <input type="text" id="api_key" name="api_key" value="{{ $api_key ?? '' }}" class="form-control" placeholder="your-api-key">
                        <p class="form-description">کلید احراز هویت API</p>
                    </div>
                </div>
                
                <div class="settings-section">
                    <h3>تنظیمات همگام‌سازی</h3>
                    
                    <div class="form-group">
                        <label for="sync_interval">فاصله همگام‌سازی (دقیقه)</label>
                        <input type="number" id="sync_interval" name="sync_interval" value="{{ $sync_interval ?? 30 }}" class="form-control" min="5" max="1440">
                        <p class="form-description">فاصله زمانی بین همگام‌سازی‌های خودکار</p>
                    </div>
                    
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="auto_sync" value="1" {{ ($auto_sync ?? false) ? 'checked' : '' }}>
                            <span>همگام‌سازی خودکار فعال باشد</span>
                        </label>
                    </div>
                </div>
                
                <div class="settings-section">
                    <h3>تنظیمات لاگ</h3>
                    
                    <div class="form-group">
                        <label for="log_level">سطح لاگ</label>
                        <select id="log_level" name="log_level" class="form-control">
                            <option value="DEBUG" {{ ($log_level ?? 'INFO') === 'DEBUG' ? 'selected' : '' }}>دیباگ (همه)</option>
                            <option value="INFO" {{ ($log_level ?? 'INFO') === 'INFO' ? 'selected' : '' }}>اطلاعات</option>
                            <option value="WARNING" {{ ($log_level ?? 'INFO') === 'WARNING' ? 'selected' : '' }}>هشدار</option>
                            <option value="ERROR" {{ ($log_level ?? 'INFO') === 'ERROR' ? 'selected' : '' }}>فقط خطاها</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="log_retention">مدت نگهداری لاگ (روز)</label>
                        <input type="number" id="log_retention" name="log_retention" value="{{ $log_retention ?? 30 }}" class="form-control" min="1" max="365">
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">ذخیره تنظیمات</button>
                    <button type="reset" class="btn btn-secondary">بازنشانی</button>
                </div>
            </form>
        </div>
    </main>
</div>

@include('components.footer')

<style>
    .settings-form {
        max-width: 800px;
    }
    
    .settings-section {
        background: white;
        padding: 25px;
        border-radius: 8px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .settings-section h3 {
        font-size: 18px;
        color: #495057;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #667eea;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #495057;
        font-size: 14px;
    }
    
    .form-control {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #ced4da;
        border-radius: 4px;
        font-size: 14px;
        transition: border-color 0.3s;
    }
    
    .form-control:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
    
    .form-description {
        margin-top: 5px;
        font-size: 12px;
        color: #6c757d;
    }
    
    .checkbox-label {
        display: flex;
        align-items: center;
        cursor: pointer;
        font-weight: normal;
    }
    
    .checkbox-label input[type="checkbox"] {
        margin-left: 8px;
        width: 18px;
        height: 18px;
        cursor: pointer;
    }
    
    .form-actions {
        display: flex;
        gap: 10px;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #e9ecef;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .btn-primary:hover {
        opacity: 0.9;
    }
</style>
