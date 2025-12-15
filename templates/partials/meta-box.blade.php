<div class="zargar-meta-box">
    <h3>{{ $meta_title ?? 'اطلاعات' }}</h3>
    <div class="meta-content">
        {{ $slot ?? '' }}
        @if(isset($meta_items) && is_array($meta_items))
            @foreach($meta_items as $key => $value)
                <div class="meta-item">
                    <strong>{{ $key }}:</strong>
                    <span>{{ $value }}</span>
                </div>
            @endforeach
        @endif
    </div>
</div>

<style>
    .zargar-meta-box {
        background: white;
        border: 1px solid #e9ecef;
        border-radius: 6px;
        margin-bottom: 20px;
        overflow: hidden;
    }
    
    .zargar-meta-box h3 {
        background: #f8f9fa;
        padding: 12px 20px;
        margin: 0;
        font-size: 15px;
        color: #495057;
        border-bottom: 1px solid #e9ecef;
    }
    
    .meta-content {
        padding: 20px;
    }
    
    .meta-item {
        padding: 10px 0;
        border-bottom: 1px solid #f8f9fa;
        font-size: 14px;
    }
    
    .meta-item:last-child {
        border-bottom: none;
    }
    
    .meta-item strong {
        display: inline-block;
        width: 150px;
        color: #6c757d;
    }
</style>
