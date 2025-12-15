@if(isset($notifications) && !empty($notifications))
    <div class="zargar-notifications">
        @foreach($notifications as $notification)
            <div class="notification notification-{{ $notification['type'] ?? 'info' }}">
                <span class="notification-icon">
                    @if(($notification['type'] ?? 'info') === 'success')
                        <span class="dashicons dashicons-yes-alt"></span>
                    @elseif(($notification['type'] ?? 'info') === 'error')
                        <span class="dashicons dashicons-dismiss"></span>
                    @elseif(($notification['type'] ?? 'info') === 'warning')
                        <span class="dashicons dashicons-warning"></span>
                    @else
                        <span class="dashicons dashicons-info"></span>
                    @endif
                </span>
                <span class="notification-message">{{ $notification['message'] }}</span>
                <button class="notification-close" onclick="this.parentElement.remove()">×</button>
            </div>
        @endforeach
    </div>
@endif

<style>
    .zargar-notifications {
        padding: 20px 30px;
    }
    
    .notification {
        display: flex;
        align-items: center;
        padding: 12px 15px;
        margin-bottom: 10px;
        border-radius: 4px;
        border-right: 4px solid;
        background: white;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    
    .notification-success {
        border-color: #28a745;
        background: #d4edda;
        color: #155724;
    }
    
    .notification-error {
        border-color: #dc3545;
        background: #f8d7da;
        color: #721c24;
    }
    
    .notification-warning {
        border-color: #ffc107;
        background: #fff3cd;
        color: #856404;
    }
    
    .notification-info {
        border-color: #17a2b8;
        background: #d1ecf1;
        color: #0c5460;
    }
    
    .notification-icon {
        margin-left: 10px;
        font-size: 20px;
    }
    
    .notification-message {
        flex: 1;
    }
    
    .notification-close {
        background: none;
        border: none;
        font-size: 24px;
        line-height: 1;
        cursor: pointer;
        opacity: 0.5;
        padding: 0;
        margin-right: 10px;
    }
    
    .notification-close:hover {
        opacity: 1;
    }
</style>
