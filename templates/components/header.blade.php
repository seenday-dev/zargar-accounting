<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Zargar Accounting' }} - حسابداری زرگر</title>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Tahoma, Arial, sans-serif;
            background: #f5f5f5;
            line-height: 1.6;
        }
        
        .zargar-wrap {
            max-width: 1400px;
            margin: 20px auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .zargar-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .zargar-header h1 {
            font-size: 24px;
            font-weight: normal;
        }
        
        .zargar-header-info {
            font-size: 13px;
            opacity: 0.9;
        }
    </style>
</head>
<body class="zargar-accounting">
    <div class="zargar-wrap">
        <div class="zargar-header">
            <h1>{{ $title ?? 'حسابداری زرگر' }}</h1>
            <div class="zargar-header-info">
                <span>کاربر: {{ $current_user->display_name }}</span>
            </div>
        </div>
