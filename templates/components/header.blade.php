<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Zargar Accounting' }} - حسابداری زرگر</title>
    
    <!-- LineIcons - Local Fixed -->
    <link rel="stylesheet" href="{{ $plugin_url }}assets/icons/lineicons-fixed.css">
    
    <link rel="stylesheet" href="{{ $plugin_url }}assets/css/main.css">
    <link rel="stylesheet" href="{{ $plugin_url }}assets/css/sidebar.css">
    <link rel="stylesheet" href="{{ $plugin_url }}assets/css/dashboard.css">
    <link rel="stylesheet" href="{{ $plugin_url }}assets/css/forms.css">
    <link rel="stylesheet" href="{{ $plugin_url }}assets/css/logs.css">
</head>
<body class="zargar-accounting">
    <div class="zargar-wrap">
        <div class="zargar-header">
            <h1>{{ $title ?? 'حسابداری زرگر' }}</h1>
            <div class="zargar-header-info">
                <span>کاربر: {{ $current_user->display_name }}</span>
            </div>
        </div>
