<nav class="zargar-navigation">
    <ul>
        <li class="{{ (isset($_GET['page']) && $_GET['page'] === 'zargar-accounting') ? 'active' : '' }}">
            <a href="?page=zargar-accounting">
                <span class="dashicons dashicons-dashboard"></span>
                داشبورد
            </a>
        </li>
        <li class="{{ (isset($_GET['page']) && $_GET['page'] === 'zargar-accounting-sync') ? 'active' : '' }}">
            <a href="?page=zargar-accounting-sync">
                <span class="dashicons dashicons-update"></span>
                همگام‌سازی
            </a>
        </li>
        <li class="{{ (isset($_GET['page']) && $_GET['page'] === 'zargar-accounting-logs') ? 'active' : '' }}">
            <a href="?page=zargar-accounting-logs">
                <span class="dashicons dashicons-list-view"></span>
                گزارش‌ها
            </a>
        </li>
    </ul>
</nav>

<style>
    .zargar-navigation {
        background: #2c3e50;
        border-bottom: 3px solid #34495e;
    }
    
    .zargar-navigation ul {
        list-style: none;
        display: flex;
        padding: 0;
        margin: 0;
    }
    
    .zargar-navigation li {
        flex: 1;
    }
    
    .zargar-navigation a {
        display: block;
        padding: 15px 20px;
        color: #ecf0f1;
        text-decoration: none;
        text-align: center;
        transition: background 0.3s;
        border-left: 1px solid #34495e;
    }
    
    .zargar-navigation li:last-child a {
        border-left: none;
    }
    
    .zargar-navigation a:hover {
        background: #34495e;
    }
    
    .zargar-navigation li.active a {
        background: #3498db;
        color: white;
    }
    
    .zargar-navigation .dashicons {
        font-size: 16px;
        vertical-align: middle;
        margin-left: 5px;
    }
</style>
