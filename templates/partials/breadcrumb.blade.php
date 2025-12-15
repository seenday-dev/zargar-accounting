<nav aria-label="breadcrumb">
    <ol class="zargar-breadcrumb">
        <li><a href="admin.php">خانه</a></li>
        @if(isset($breadcrumbs) && is_array($breadcrumbs))
            @foreach($breadcrumbs as $crumb)
                @if(isset($crumb['url']))
                    <li><a href="{{ $crumb['url'] }}">{{ $crumb['title'] }}</a></li>
                @else
                    <li class="active">{{ $crumb['title'] }}</li>
                @endif
            @endforeach
        @endif
    </ol>
</nav>

<style>
    .zargar-breadcrumb {
        list-style: none;
        display: flex;
        padding: 15px 30px;
        background: #e9ecef;
        font-size: 13px;
        margin: 0;
    }
    
    .zargar-breadcrumb li {
        display: flex;
        align-items: center;
    }
    
    .zargar-breadcrumb li:not(:last-child)::after {
        content: "←";
        margin: 0 10px;
        color: #6c757d;
    }
    
    .zargar-breadcrumb a {
        color: #667eea;
        text-decoration: none;
    }
    
    .zargar-breadcrumb a:hover {
        text-decoration: underline;
    }
    
    .zargar-breadcrumb li.active {
        color: #495057;
        font-weight: bold;
    }
</style>
