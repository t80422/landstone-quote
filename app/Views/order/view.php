<?= $this->extend('_layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid mt-4">
    <!-- Header & Actions -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-file-earmark-text me-2"></i>訂單內容</h2>
        <div class="d-flex gap-2">
            <a href="<?= url_to('OrderController::print', $data['o_id']) ?>" class="btn btn-success btn-sm" target="_blank">
                <i class="bi bi-printer me-1"></i>列印
            </a>
            <a href="<?= url_to('OrderController::edit', $data['o_id']) ?>" class="btn btn-primary btn-sm">
                <i class="bi bi-pencil-square me-1"></i>編輯
            </a>
            <a href="<?= url_to('OrderController::index') ?>" class="btn btn-outline-secondary btn-sm">
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

    <div class="row g-4">
        <!-- 訂單資訊 & 客戶資料 -->
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light py-3">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h5 class="mb-0 text-primary fw-bold">訂單編號：<?= esc($data['o_number']) ?></h5>
                        </div>
                        <div class="col-md-6 text-md-end text-muted small">
                            建立時間：<?= esc($data['created_at'] ?? date('Y-m-d H:i:s')) ?>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <!-- 左側：基本資料 -->
                        <div class="col-md-6 border-end">
                            <h6 class="text-secondary mb-3 border-bottom pb-2">基本資料</h6>
                            <div class="row">
                                <div class="col-6">
                                    <table class="table table-borderless table-sm mb-0">
                                        <tr>
                                            <td class="text-muted" width="80">訂單日期</td>
                                            <td class="fw-bold"><?= esc($data['o_date']) ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">訂單狀態</td>
                                            <td>
                                                <?php
                                                $statusMap = [
                                                    'processing' => ['處理中', 'primary'],
                                                    'completed' => ['已完結', 'success'],
                                                    'cancelled' => ['已取消', 'secondary']
                                                ];
                                                $status = $statusMap[$data['o_status'] ?? ''] ?? ['未知', 'secondary'];
                                                ?>
                                                <span class="badge bg-<?= $status[1] ?>"><?= $status[0] ?></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">付款狀態</td>
                                            <td>
                                                <?php
                                                $paymentMap = [
                                                    'unpaid' => ['未收款', 'danger'],
                                                    'partial' => ['部分收款', 'warning'],
                                                    'paid' => ['已結清', 'success']
                                                ];
                                                $payment = $paymentMap[$data['o_payment_status'] ?? ''] ?? ['-', 'secondary'];
                                                ?>
                                                <span class="badge bg-<?= $payment[1] ?>"><?= $payment[0] ?></span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-6">
                                    <table class="table table-borderless table-sm mb-0">
                                        <tr>
                                            <td class="text-muted" width="80">預交期</td>
                                            <td><?= esc($data['o_delivery_date'] ?? '-') ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">出貨狀態</td>
                                            <td>
                                                <?php
                                                $shipmentMap = [
                                                    'preparing' => ['備貨中', 'info'],
                                                    'partial' => ['部分出貨', 'primary'],
                                                    'shipped' => ['已全出', 'success']
                                                ];
                                                $shipment = $shipmentMap[$data['o_shipment_status'] ?? ''] ?? ['-', 'secondary'];
                                                ?>
                                                <span class="badge bg-<?= $shipment[1] ?>"><?= $shipment[0] ?></span>
                                            </td>
                                        </tr>
                                        <?php if (!empty($data['o_invoice_number'])): ?>
                                            <tr>
                                                <td class="text-muted">發票號碼</td>
                                                <td><?= esc($data['o_invoice_number']) ?></td>
                                            </tr>
                                        <?php endif; ?>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- 右側：客戶資料 -->
                        <div class="col-md-6">
                            <h6 class="text-secondary mb-3 border-bottom pb-2">客戶資料</h6>
                            <div class="row">
                                <div class="col-6">
                                    <table class="table table-borderless table-sm mb-0">
                                        <tr>
                                            <td class="text-muted" width="80">客戶名稱</td>
                                            <td class="fw-bold"><?= esc($data['customer']['c_name'] ?? '-') ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">統一編號</td>
                                            <td><?= esc($data['customer']['c_tax_id'] ?? '-') ?></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-6">
                                    <table class="table table-borderless table-sm mb-0">
                                        <tr>
                                            <td class="text-muted" width="80">聯絡人</td>
                                            <td><?= esc($data['contact']['cc_name'] ?? '-') ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">聯絡電話</td>
                                            <td><?= esc($data['contact']['cc_phone'] ?? '-') ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 商品明細 -->
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0 card-title">商品明細</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4" style="width: 80px;">圖片</th>
                                    <th>商品資訊</th>
                                    <th class="text-center" style="width: 100px;">數量 / 單位</th>
                                    <th class="text-end" style="width: 120px;">單價</th>
                                    <th class="text-center" style="width: 100px;">折扣</th>
                                    <th class="text-end pe-4" style="width: 150px;">金額</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($data['items'])): ?>
                                    <?php foreach ($data['items'] as $item): ?>
                                        <?php
                                        // 處理圖片路徑
                                        $imagePath = !empty($item['p_image']) ? base_url($item['p_image']) : base_url('assets/images/placeholder.png');

                                        // 處理規格合併顯示
                                        // 注意：這裡使用 oi_ 前綴，假設這些欄位在資料庫中已存在 (因為 form 中有使用)
                                        // 如果資料庫中沒有這些欄位，則可能需要從 products 表關聯獲取，但依照 form 邏輯應是已儲存
                                        $specs = array_filter([
                                            ($item['oi_style'] ?? '') ? "款式:{$item['oi_style']}" : null,
                                            ($item['oi_color'] ?? '') ? "顏色:{$item['oi_color']}" : null,
                                            ($item['oi_size'] ?? '') ? "尺寸:{$item['oi_size']}" : null,
                                        ]);
                                        $specString = implode(' / ', $specs);
                                        ?>
                                        <tr>
                                            <td class="ps-4">
                                                <div class="ratio ratio-1x1 bg-light border rounded" style="width: 60px;">
                                                    <img src="<?= esc($imagePath) ?>" alt=""
                                                        class="img-fluid object-fit-cover rounded">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="fw-bold text-dark"><?= esc($item['p_name'] ?? '未知商品') ?></div>
                                                <div class="d-flex align-items-center gap-2 mt-1">
                                                    <?php if (!empty($item['pc_name'])): ?>
                                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-10 rounded-pill px-2">
                                                            <?= esc($item['pc_name']) ?>
                                                        </span>
                                                    <?php endif; ?>
                                                    <span class="small text-muted"><?= esc($item['p_code'] ?? '') ?></span>
                                                </div>

                                                <?php if (!empty($item['oi_supplier'])): ?>
                                                    <div class="small text-secondary mt-1">
                                                        <i class="bi bi-shop me-1"></i>供應商：<?= esc($item['oi_supplier']) ?>
                                                    </div>
                                                <?php endif; ?>

                                                <?php if ($specString): ?>
                                                    <div class="small text-info mt-1">
                                                        <i class="bi bi-tags me-1"></i><?= esc($specString) ?>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-light text-dark border fs-6">
                                                    <?= $item['oi_quantity'] ?? 0 ?>
                                                </span>
                                                <?php if (!empty($item['oi_shipped_quantity']) && $item['oi_shipped_quantity'] > 0): ?>
                                                    <div class="mt-1">
                                                        <span class="badge bg-success bg-opacity-75" title="已出貨數量">
                                                            已出 <?= $item['oi_shipped_quantity'] ?>
                                                        </span>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-end">NT$ <?= number_format($item['oi_unit_price'] ?? 0, 0) ?></td>
                                            <td class="text-center text-danger">
                                                <?php if (($item['oi_discount'] ?? 0) > 0): ?>
                                                    -<?= floatval($item['oi_discount']) ?>%
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-end fw-bold pe-4">
                                                <?php
                                                // 簡單計算金額，以防資料庫欄位缺失
                                                $amount = ($item['oi_quantity'] ?? 0) * ($item['oi_unit_price'] ?? 0) * (1 - ($item['oi_discount'] ?? 0) / 100);
                                                ?>
                                                NT$ <?= number_format($amount, 0) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">無商品項目</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white p-4">
                    <div class="row justify-content-end">
                        <div class="col-md-5 col-lg-4">
                            <table class="table table-sm table-borderless mb-0">
                                <!-- 訂單表單中沒有額外的稅額運費欄位，直接顯示總金額 -->
                                <tr class="border-top border-2">
                                    <td class="pt-2 fs-5 fw-bold text-primary">總金額</td>
                                    <td class="pt-2 fs-5 fw-bold text-end text-primary">NT$ <?= number_format($data['o_total_amount'] ?? 0, 0) ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 備註 -->
        <?php if (!empty($data['o_notes'])): ?>
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">備註</h6>
                    </div>
                    <div class="card-body">
                        <?= nl2br(esc($data['o_notes'])) ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- 列印樣式 -->
<style>
    @media print {

        /* 隱藏非必要元素 */
        .btn,
        .alert,
        header,
        footer,
        .bi {
            display: none !important;
        }

        /* 確保背景色列印 */
        .card-header,
        .table-light,
        .bg-light,
        .badge {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* 調整列印版面 */
        .card {
            border: none !important;
            box-shadow: none !important;
        }

        .card-header {
            border-bottom: 2px solid #000 !important;
        }

        /* 強制分頁設定 */
        .page-break {
            page-break-before: always;
        }
    }
</style>

<?= $this->endSection() ?>