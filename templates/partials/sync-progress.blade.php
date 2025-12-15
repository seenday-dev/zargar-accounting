<div class="sync-progress-container">
    <div class="progress-header">
        <h4>{{ $progress_title ?? 'در حال پردازش...' }}</h4>
        <span class="progress-percentage">{{ $progress_percent ?? 0 }}%</span>
    </div>
    
    <div class="progress-bar">
        <div class="progress-fill" style="width: {{ $progress_percent ?? 0 }}%"></div>
    </div>
    
    @if(isset($progress_message))
        <div class="progress-message">{{ $progress_message }}</div>
    @endif
</div>

<style>
    .sync-progress-container {
        background: white;
        padding: 20px;
        border-radius: 6px;
        border: 1px solid #e9ecef;
        margin: 20px 0;
    }
    
    .progress-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }
    
    .progress-header h4 {
        margin: 0;
        font-size: 16px;
        color: #495057;
    }
    
    .progress-percentage {
        font-size: 18px;
        font-weight: bold;
        color: #667eea;
    }
    
    .progress-bar {
        height: 24px;
        background: #e9ecef;
        border-radius: 12px;
        overflow: hidden;
        position: relative;
    }
    
    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        transition: width 0.5s ease;
        border-radius: 12px;
    }
    
    .progress-message {
        margin-top: 10px;
        font-size: 13px;
        color: #6c757d;
        text-align: center;
    }
</style>
