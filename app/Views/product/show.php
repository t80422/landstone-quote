<?= $this->extend('_layout') ?>
<?= $this->section('content') ?>

<?php
function formatImagePath($path)
{
    if (empty($path)) {
        return null;
    }

    if (strpos($path, 'http://') === 0 || strpos($path, 'https://') === 0) {
        return $path;
    }

    return base_url(ltrim($path, '/'));
}

$imagePath = formatImagePath($data['p_image'] ?? '');
?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2><i class="bi bi-box-seam me-2"></i>商品詳細</h2>
        <div class="d-flex gap-2">
            <a href="<?= url_to('ProductController::edit', $data['p_id']) ?>" class="btn btn-primary btn-sm">
                <i class="bi bi-pencil-square me-1"></i>編輯
            </a>
            <a href="<?= url_to('ProductController::index') ?>" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>返回列表
            </a>
        </div>
    </div>

    <!-- 成功/錯誤訊息 -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row g-3">
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <?php if ($imagePath): ?>
                        <img src="<?= esc($imagePath) ?>" alt="<?= esc($data['p_name']) ?>"
                            class="img-fluid rounded mb-3" style="max-height: 300px; object-fit: cover;">
                    <?php else: ?>
                        <div class="bg-light d-flex align-items-center justify-content-center mb-3"
                            style="width: 100%; height: 300px; border-radius: 8px;">
                            <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                        </div>
                    <?php endif; ?>
                    <div class="text-muted small">
                        建立時間：<?= esc($data['p_created_at'] ?? '-') ?><br>
                        更新時間：<?= esc($data['p_updated_at'] ?? '-') ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="text-muted small">產品編號</div>
                            <div class="fw-bold h5"><?= esc($data['p_code'] ?? '-') ?></div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">產品名稱</div>
                            <div class="fw-bold h5"><?= esc($data['p_name'] ?? '-') ?></div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="text-muted small">分類</div>
                            <div>
                                <?php if (!empty($data['pc_name'])): ?>
                                    <span class="badge bg-info"><i class="bi bi-tag me-1"></i><?= esc($data['pc_name']) ?></span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">供應商</div>
                            <div><?= esc($data['p_supplier'] ?? '-') ?></div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="text-muted small">顏色/花色</div>
                            <div><?= esc($data['p_color'] ?? '-') ?></div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">尺寸</div>
                            <div><?= esc($data['p_size'] ?? '-') ?></div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="text-muted small">售價</div>
                            <div class="fw-bold text-primary"><?= $data['p_standard_price'] !== null ? number_format($data['p_standard_price'], 2) : '-' ?></div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">進貨成本</div>
                            <div class="fw-bold text-success"><?= $data['p_cost_price'] !== null ? number_format($data['p_cost_price'], 2) : '-' ?></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="text-muted small">詳細規格</div>
                        <div class="p-3 bg-light rounded"><?= nl2br(esc($data['p_specifications'] ?? '-')) ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>