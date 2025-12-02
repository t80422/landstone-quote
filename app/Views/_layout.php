<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Land Stone - 報價系統</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Tom Select CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/custom.css') ?>">
</head>

<body>
    <!-- header -->
    <nav class="navbar navbar-expand-lg navbar-light navbar-bg-color">
        <div class="container-fluid">
            <button class="btn btn-outline-dark" id="sidebarToggle">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a class="navbar-brand d-flex align-items-center gap-2 ms-3" href="<?= url_to('HomeController::index') ?>">
            Land Stone 報價系統
            </a>
        </div>
    </nav>
    <!-- content -->
    <div id="wrapper">
        <!-- Sidebar -->
        <div class="border-right" id="sidebar-wrapper">
            <div class="sidebar-heading">功能列</div>
            <div class="list-group list-group-flush">
                <a href="<?= url_to('CustomerController::index') ?>" class="list-group-item list-group-item-action">
                    <i class="bi bi-people"></i> 客戶資料管理
                </a>
                <a href="<?= url_to('ProductController::index') ?>" class="list-group-item list-group-item-action">
                    <i class="bi bi-box-seam"></i> 商品資料管理
                </a>
                <a href="<?= url_to('QuoteController::index') ?>" class="list-group-item list-group-item-action">
                    <i class="bi bi-file-text"></i> 報價單管理
                </a>
                <a href="<?= url_to('OrderController::index') ?>" class="list-group-item list-group-item-action">
                    <i class="bi bi-receipt"></i> 訂單管理
                </a>
            </div>

            <!-- 設定區塊 -->
            <div class="sidebar-heading mt-3">設定</div>
            <div class="list-group list-group-flush">
                <a href="<?= url_to('PaymentMethodController::index') ?>" class="list-group-item list-group-item-action">
                    <i class="bi bi-cash-coin"></i> 結帳方式管理
                </a>
            </div>
        </div>
        <!-- Page Content -->
        <div id="page-content-wrapper">
            <div class="container-fluid">
                <?= $this->renderSection('content') ?>
            </div>
        </div>
    </div>
    
    <!-- Tom Select JS -->
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    <!-- Bootstrap JS (放在最後，因為可能會影響其他元件) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    
    <script>
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.getElementById('wrapper').classList.toggle('toggled');
        });

        // 動態調整子選單位置
        document.addEventListener('DOMContentLoaded', function() {
            const dropdowns = document.querySelectorAll('.nav-dropdown');

            dropdowns.forEach(function(dropdown) {
                const dropdownMenu = dropdown.querySelector('.dropdown-menu-custom');

                if (dropdownMenu) {
                    dropdown.addEventListener('mouseenter', function() {
                        const rect = this.getBoundingClientRect();
                        dropdownMenu.style.top = rect.top + 'px';
                    });
                }
            });
        });
    </script>
</body>

</html>