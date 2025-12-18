<?php

/**
 * Helper function: 顯示欄位錯誤訊息
 */
function showFieldError($fieldName)
{
    $errors = session()->getFlashdata('errors');
    if (isset($errors[$fieldName])) {
        return '<div class="invalid-feedback d-block"><i class="bi bi-exclamation-circle me-1"></i>' . esc($errors[$fieldName]) . '</div>';
    }
    return '';
}

/**
 * Helper function: 檢查欄位是否有錯誤並返回 class
 */
function getFieldClass($fieldName)
{
    $errors = session()->getFlashdata('errors');
    return isset($errors[$fieldName]) ? 'is-invalid' : '';
}
?>

<?= $this->extend('_layout') ?>
<?= $this->section('content') ?>

<div class="container mt-4">
    <!-- 頁面標題 -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>
            <i class="bi bi-<?= $isEdit ? 'pencil-square' : 'plus-circle' ?> me-2"></i>
            <?= $isEdit ? '編輯' : '新增' ?>商品資料
        </h2>
    </div>

    <!-- 全域錯誤訊息 -->
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- 表單卡片 -->
    <div class="card shadow-sm">
        <div class="card-body p-4">
            <form id="productForm" action="<?= url_to('ProductController::save') ?>" method="post" enctype="multipart/form-data" novalidate>
                <?php if ($isEdit): ?>
                    <input type="hidden" name="p_id" value="<?= $data['p_id'] ?? old('p_id') ?>">
                <?php endif; ?>

                <!-- 基本資料區塊 -->
                <div class="mb-4">
                    <h5 class="border-bottom pb-2 mb-3">
                        <i class="bi bi-box me-2 text-primary"></i>基本資料
                    </h5>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="productCode" class="form-label">產品編號</label>
                            <div class="input-group">
                                <input
                                    type="text"
                                    class="form-control bg-light"
                                    id="productCode"
                                    value="<?= old('p_code', $data['p_code'] ?? '系統自動產生') ?>"
                                    readonly>
                            </div>
                            <div class="form-text">產品編號由系統自動產生</div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="category" class="form-label">產品分類</label>
                            <select
                                class="form-select <?= getFieldClass('p_pc_id') ?>"
                                id="category"
                                name="p_pc_id"
                                aria-describedby="categoryError"
                                required>
                                <option value="">請選擇分類</option>
                                <?php if (isset($categories) && is_array($categories)): ?>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= esc($category['pc_id']) ?>"
                                            <?= (old('p_pc_id', $data['p_pc_id'] ?? '') == $category['pc_id']) ? 'selected' : '' ?>>
                                            <?= esc($category['pc_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <?= showFieldError('p_pc_id') ?>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="productName" class="form-label">
                                產品名稱/款式 <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                class="form-control <?= getFieldClass('p_name') ?>"
                                id="productName"
                                name="p_name"
                                value="<?= old('p_name', $data['p_name'] ?? '') ?>"
                                placeholder="請輸入產品名稱"
                                required
                                aria-describedby="productNameError">
                            <?= showFieldError('p_name') ?>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="supplier" class="form-label">供應商</label>
                            <textarea
                                class="form-control <?= getFieldClass('p_supplier') ?>"
                                id="supplier"
                                name="p_supplier"
                                rows="3"
                                placeholder="供應商名稱"><?= old('p_supplier', $data['p_supplier'] ?? '') ?>
                            </textarea>
                            <?= showFieldError('p_supplier') ?>
                            <div class="form-text">使用"、"分隔多個供應商</div>
                        </div>
                    </div>
                </div>

                <!-- 屬性規格區塊 -->
                <div class="mb-4">
                    <h5 class="border-bottom pb-2 mb-3">
                        <i class="bi bi-tags me-2 text-primary"></i>屬性與規格
                    </h5>
                    <!-- 第一行：主要屬性 -->
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="color" class="form-label">顏色/花色</label>
                            <textarea
                                class="form-control <?= getFieldClass('p_color') ?>"
                                id="color"
                                name="p_color"
                                rows="3"
                                placeholder="顏色或花色"><?= old('p_color', $data['p_color'] ?? '') ?></textarea>
                            <?= showFieldError('p_color') ?>
                            <div class="form-text">使用"、"分隔多個顏色/花色</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="size" class="form-label">尺寸</label>
                            <textarea
                                class="form-control <?= getFieldClass('p_size') ?>"
                                id="size"
                                name="p_size"
                                rows="3"
                                placeholder="產品尺寸"><?= old('p_size', $data['p_size'] ?? '') ?></textarea>
                            <?= showFieldError('p_size') ?>
                            <div class="form-text">使用"、"分隔多個尺寸</div>
                        </div>
                    </div>

                    <!-- 第二行：價格與成本 -->
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="sellingPrice" class="form-label">
                                售價 <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input
                                    type="number"
                                    class="form-control <?= getFieldClass('p_standard_price') ?>"
                                    id="sellingPrice"
                                    name="p_standard_price"
                                    value="<?= old('p_standard_price', $data['p_standard_price'] ?? '') ?>"
                                    placeholder="0"
                                    min="0"
                                    step="0.01"
                                    required>
                                <span class="input-group-text">元</span>
                            </div>
                            <?= showFieldError('p_standard_price') ?>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="costPrice" class="form-label">進貨成本</label>
                            <div class="input-group">
                                <input
                                    type="number"
                                    class="form-control <?= getFieldClass('p_cost_price') ?>"
                                    id="costPrice"
                                    name="p_cost_price"
                                    value="<?= old('p_cost_price', $data['p_cost_price'] ?? '') ?>"
                                    placeholder="0"
                                    min="0"
                                    step="0.01">
                                <span class="input-group-text">元</span>
                            </div>
                            <?= showFieldError('p_cost_price') ?>
                        </div>
                    </div>

                    <!-- 第三行：詳細規格 -->
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="specifications" class="form-label">詳細規格說明</label>
                            <textarea
                                class="form-control <?= getFieldClass('p_specifications') ?>"
                                id="specifications"
                                name="p_specifications"
                                rows="3"
                                placeholder="請輸入產品的詳細規格描述..."><?= old('p_specifications', $data['p_specifications'] ?? '') ?></textarea>
                            <?= showFieldError('p_specifications') ?>
                        </div>
                    </div>
                </div>

                <!-- 產品圖片區塊 -->
                <div class="mb-4">
                    <h5 class="border-bottom pb-2 mb-3">
                        <i class="bi bi-image me-2 text-primary"></i>產品圖片
                    </h5>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <?php if (!empty($data['p_image'])): ?>
                                <?php
                                $currentImagePath = $data['p_image'];
                                if (strpos($currentImagePath, 'http://') !== 0 && strpos($currentImagePath, 'https://') !== 0) {
                                    $currentImagePath = base_url(ltrim($currentImagePath, '/'));
                                }
                                ?>
                                <div class="mb-3">
                                    <label class="form-label">目前圖片</label>
                                    <div>
                                        <img src="<?= esc($currentImagePath) ?>"
                                            alt="產品圖片"
                                            class="img-thumbnail"
                                            style="max-width: 300px; max-height: 300px;">
                                    </div>
                                </div>
                            <?php endif; ?>
                            <label for="productImage" class="form-label">
                                <?= !empty($data['p_image']) ? '更換圖片' : '上傳圖片' ?>
                            </label>
                            <input
                                type="file"
                                class="form-control <?= getFieldClass('p_image') ?>"
                                id="productImage"
                                name="p_image"
                                accept="image/*"
                                aria-describedby="productImageError">
                            <?= showFieldError('p_image') ?>
                            <div class="form-text">建議尺寸：800x800 像素，支援 JPG、PNG 格式</div>
                        </div>
                    </div>
                </div>

                <!-- 時間戳記資訊 (僅編輯時顯示) -->
                <?php if (!empty($isEdit) && isset($data)): ?>
                    <div class="mb-4">
                        <h5 class="border-bottom pb-2 mb-3">
                            <i class="bi bi-clock-history me-2 text-primary"></i>系統資訊
                        </h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">
                                    <i class="bi bi-calendar-plus me-1"></i>新增時間
                                </label>
                                <div class="p-2 bg-light rounded">
                                    <?= esc($data['p_created_at'] ?? '無資料') ?>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">
                                    <i class="bi bi-calendar-check me-1"></i>更新時間
                                </label>
                                <div class="p-2 bg-light rounded">
                                    <?= esc($data['p_updated_at'] ?? '無資料') ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- 必填欄位說明 -->
                <div class="alert alert-info py-2 mb-4">
                    <i class="bi bi-info-circle me-2"></i>
                    標示 <span class="text-danger">*</span> 為必填欄位
                </div>

                <!-- 表單按鈕 -->
                <div class="d-flex gap-2 justify-content-end">
                    <a href="<?= url_to('ProductController::index') ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i>取消
                    </a>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="bi bi-check-circle me-1"></i>儲存
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // 表單提交處理 - 防止重複提交
    document.getElementById('productForm').addEventListener('submit', function(e) {
        const submitBtn = document.getElementById('submitBtn');

        // 檢查表單驗證
        if (!this.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
            this.classList.add('was-validated');
            return false;
        }

        // 防止重複提交
        if (submitBtn.disabled) {
            e.preventDefault();
            return false;
        }

        // 禁用提交按鈕並顯示載入狀態
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>儲存中...';
    });

    // 圖片預覽功能
    document.getElementById('productImage')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                // 可以在這裡添加圖片預覽功能
                console.log('圖片已選擇:', file.name);
            };
            reader.readAsDataURL(file);
        }
    });
</script>

<?= $this->endSection() ?>