my-plugin/
├── my-plugin.php
│
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
│
├── includes/
│   ├── class-main.php
│
│
├── storage/
│   └── logs/
│       ├── api.log
│       ├── woocommerce-sync.log
│       ├── price-sync.log
│       └── factor-sync.log
│
└── templates/
    ├── components/
    │   ├── header.php
    │   ├── footer.php
    │   ├── navigation.php
    │   └── sidebar.php
    │
    ├── partials/
    │   ├── breadcrumb.php
    │   ├── pagination.php
    │   └── meta-box.php
    │
    ├── dashboard.php
    ├── settings.php
    └── logs.php


























---------------------------------------------------------------------------------------------------------------------------------
    my-plugin/
├── my-plugin.php
│
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
│
├── includes/
│   ├── core/
│   │   ├── class-plugin.php
│   │   ├── class-loader.php
│   │   └── class-autoloader.php
│   │
│   ├── admin/
│   │   ├── class-admin-menu.php
│   │   ├── class-settings.php
│   │   └── class-dashboard.php
│   │
│   ├── api/
│   │   ├── class-api-client.php
│   │   ├── class-api-auth.php
│   │   └── class-api-error-handler.php
│   │
│   ├── sync/
│   │   ├── class-sync-manager.php
│   │   ├── class-order-sync.php
│   │   ├── class-product-sync.php
│   │   └── class-price-sync.php
│   │
│   ├── database/
│   │   ├── class-sync-queue.php
│   │   ├── class-sync-history.php
│   │   └── class-database-manager.php
│   │
│   ├── validators/
│   │   ├── class-order-validator.php
│   │   └── class-product-validator.php
│   │
│   ├── transformers/
│   │   ├── class-order-transformer.php
│   │   └── class-product-transformer.php
│   │
│   ├── logger/
│   │   ├── class-logger.php
│   │   └── class-log-viewer.php
│   │
│   └── helpers/
│       ├── functions-general.php
│       ├── functions-sync.php
│       └── functions-formatting.php
│
├── public/
│   └── images/
│
├── storage/
│   ├── logs/
│   │   ├── api-{date}.log
│   │   ├── sync-{date}.log
│   │   ├── error-{date}.log
│   │   └── debug-{date}.log
│   │
│   └── cache/
│       ├── tokens/
│       └── temp/
│
└── templates/
    ├── admin/
    │   ├── dashboard.php
    │   ├── settings.php
    │   ├── sync-status.php
    │   ├── sync-history.php
    │   └── logs.php
    │
    ├── components/
    │   ├── header.php
    │   ├── footer.php
    │   ├── navigation.php
    │   └── sidebar.php
    │
    └── partials/
        ├── breadcrumb.php
        ├── pagination.php
        ├── notifications.php
        ├── sync-progress.php
        └── meta-box.php