@if(isset($pagination) && $pagination['total_pages'] > 1)
    <div class="zargar-pagination">
        <div class="pagination-info">
            صفحه {{ $pagination['current_page'] }} از {{ $pagination['total_pages'] }}
            (مجموع {{ $pagination['total_items'] }} مورد)
        </div>
        
        <div class="pagination-links">
            @if($pagination['current_page'] > 1)
                <a href="{{ $pagination['base_url'] }}&paged=1" class="page-link">اول</a>
                <a href="{{ $pagination['base_url'] }}&paged={{ $pagination['current_page'] - 1 }}" class="page-link">قبلی</a>
            @endif
            
            @for($i = max(1, $pagination['current_page'] - 2); $i <= min($pagination['total_pages'], $pagination['current_page'] + 2); $i++)
                @if($i == $pagination['current_page'])
                    <span class="page-link active">{{ $i }}</span>
                @else
                    <a href="{{ $pagination['base_url'] }}&paged={{ $i }}" class="page-link">{{ $i }}</a>
                @endif
            @endfor
            
            @if($pagination['current_page'] < $pagination['total_pages'])
                <a href="{{ $pagination['base_url'] }}&paged={{ $pagination['current_page'] + 1 }}" class="page-link">بعدی</a>
                <a href="{{ $pagination['base_url'] }}&paged={{ $pagination['total_pages'] }}" class="page-link">آخر</a>
            @endif
        </div>
    </div>
@endif

<style>
    .zargar-pagination {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 30px;
        background: #f8f9fa;
        border-top: 1px solid #e9ecef;
    }
    
    .pagination-info {
        color: #6c757d;
        font-size: 13px;
    }
    
    .pagination-links {
        display: flex;
        gap: 5px;
    }
    
    .page-link {
        display: inline-block;
        padding: 6px 12px;
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        color: #667eea;
        text-decoration: none;
        font-size: 13px;
        transition: all 0.3s;
    }
    
    .page-link:hover {
        background: #667eea;
        color: white;
    }
    
    .page-link.active {
        background: #667eea;
        color: white;
        border-color: #667eea;
        font-weight: bold;
    }
</style>
