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

$productCategories = $productCategories ?? [];
$deliveryAddressMissing = $deliveryAddressMissing ?? false;
$selectedCustomerId = old('o_c_id', $data['o_c_id'] ?? '');
$selectedDeliveryAddressId = old('o_cda_id', $data['o_cda_id'] ?? '');

$productCategories = $productCategories ?? [];
?>

<?= $this->extend('_layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid mt-4">
    <!-- 頁面標題 -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>
            <i class="bi bi-<?= $isEdit ? 'pencil-square' : 'plus-circle' ?> me-2"></i>
            <?= $isEdit ? '編輯' : '新增' ?>訂單
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
            <form id="orderForm" action="<?= url_to('OrderController::save') ?>" method="post" novalidate>
                <?php if ($isEdit): ?>
                    <input type="hidden" name="o_id" value="<?= $data['o_id'] ?? old('o_id') ?>">
                <?php endif; ?>

                <!-- 基本資料區塊 -->
                <div class="mb-4">
                    <h5 class="border-bottom pb-2 mb-3">
                        <i class="bi bi-file-earmark-text me-2 text-primary"></i>基本資料
                    </h5>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="orderNumber" class="form-label">訂單編號</label>
                            <input
                                type="text"
                                class="form-control <?= getFieldClass('o_number') ?>"
                                id="orderNumber"
                                name="o_number"
                                value="<?= old('o_number', $data['o_number'] ?? $orderNumber ?? '') ?>"
                                required
                                readonly>
                            <?= showFieldError('o_number') ?>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="orderDate" class="form-label">
                                訂單日期 <span class="text-danger">*</span>
                            </label>
                            <input
                                type="date"
                                class="form-control <?= getFieldClass('o_date') ?>"
                                id="orderDate"
                                name="o_date"
                                value="<?= old('o_date', $data['o_date'] ?? date('Y-m-d')) ?>"
                                required>
                            <?= showFieldError('o_date') ?>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="deliveryDate" class="form-label">預交期</label>
                            <input
                                type="date"
                                class="form-control <?= getFieldClass('o_delivery_date') ?>"
                                id="deliveryDate"
                                name="o_delivery_date"
                                value="<?= old('o_delivery_date', $data['o_delivery_date'] ?? '') ?>">
                            <?= showFieldError('o_delivery_date') ?>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="customer" class="form-label">
                                客戶 <span class="text-danger">*</span>
                            </label>
                            <select
                                class="form-select <?= getFieldClass('o_c_id') ?>"
                                id="customer"
                                name="o_c_id"
                                required>
                                <option value=""></option>
                                <?php foreach ($customers as $customer): ?>
                                    <option value="<?= $customer['c_id'] ?>"
                                        <?= (old('o_c_id', $data['o_c_id'] ?? '') == $customer['c_id']) ? 'selected' : '' ?>>
                                        <?= esc($customer['c_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?= showFieldError('o_c_id') ?>
                        </div>
                    </div>

                    <div class="row align-items-start mb-3">
                        <div class="col-md-6 mb-3">
                            <label for="deliveryAddressSelect" class="form-label">
                                送貨地址 <span class="text-danger">*</span>
                            </label>
                            <select
                                class="form-select <?= getFieldClass('o_cda_id') ?>"
                                id="deliveryAddressSelect"
                                name="o_cda_id"
                                required
                                data-endpoint="<?= base_url('customer/delivery-addresses') ?>"
                                data-initial-customer="<?= esc($selectedCustomerId) ?>"
                                data-selected-id="<?= esc($selectedDeliveryAddressId) ?>">
                                <option value=""></option>
                            </select>
                            <?= showFieldError('o_cda_id') ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <?php if ($deliveryAddressMissing): ?>
                                <div class="alert alert-warning mb-2">
                                    原送貨地址已被刪除，請重新選擇。
                                </div>
                            <?php endif; ?>
                            <div id="deliveryAddressNotice" class="alert alert-warning d-none mb-0"></div>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label">地址名稱</label>
                            <input type="text" class="form-control" id="deliveryAddressName" value="" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">收件人</label>
                            <input type="text" class="form-control" id="deliveryContact" value="" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">連絡電話</label>
                            <input type="text" class="form-control" id="deliveryPhone" value="" readonly>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">送貨地址</label>
                            <textarea class="form-control" id="deliveryAddressText" rows="2" readonly></textarea>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="status" class="form-label">訂單狀態</label>
                            <select class="form-select <?= getFieldClass('o_status') ?>" id="status" name="o_status">
                                <option value="processing" <?= (old('o_status', $data['o_status'] ?? '') == 'processing') ? 'selected' : '' ?>>處理中</option>
                                <option value="completed" <?= (old('o_status', $data['o_status'] ?? '') == 'completed') ? 'selected' : '' ?>>已完結</option>
                                <option value="cancelled" <?= (old('o_status', $data['o_status'] ?? '') == 'cancelled') ? 'selected' : '' ?>>已取消</option>
                            </select>
                            <?= showFieldError('o_status') ?>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="paymentStatus" class="form-label">付款狀態</label>
                            <select class="form-select <?= getFieldClass('o_payment_status') ?>" id="paymentStatus" name="o_payment_status">
                                <option value="unpaid" <?= (old('o_payment_status', $data['o_payment_status'] ?? '') == 'unpaid') ? 'selected' : '' ?>>未收款</option>
                                <option value="partial" <?= (old('o_payment_status', $data['o_payment_status'] ?? '') == 'partial') ? 'selected' : '' ?>>部分收款</option>
                                <option value="paid" <?= (old('o_payment_status', $data['o_payment_status'] ?? '') == 'paid') ? 'selected' : '' ?>>已結清</option>
                            </select>
                            <?= showFieldError('o_payment_status') ?>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="shipmentStatus" class="form-label">出貨狀態</label>
                            <select class="form-select <?= getFieldClass('o_shipment_status') ?>" id="shipmentStatus" name="o_shipment_status">
                                <option value="preparing" <?= (old('o_shipment_status', $data['o_shipment_status'] ?? '') == 'preparing') ? 'selected' : '' ?>>備貨中</option>
                                <option value="partial" <?= (old('o_shipment_status', $data['o_shipment_status'] ?? '') == 'partial') ? 'selected' : '' ?>>部分出貨</option>
                                <option value="shipped" <?= (old('o_shipment_status', $data['o_shipment_status'] ?? '') == 'shipped') ? 'selected' : '' ?>>已全出</option>
                            </select>
                            <?= showFieldError('o_shipment_status') ?>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="invoiceNumber" class="form-label">發票號碼</label>
                            <input
                                type="text"
                                class="form-control <?= getFieldClass('o_invoice_number') ?>"
                                id="invoiceNumber"
                                name="o_invoice_number"
                                value="<?= old('o_invoice_number', $data['o_invoice_number'] ?? '') ?>"
                                placeholder="請輸入發票號碼">
                            <?= showFieldError('o_invoice_number') ?>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">來源報價單</label>
                            <?php if (!empty($data['o_q_id'])): ?>
                                <div>
                                    <a href="<?= url_to('QuoteController::edit', $data['o_q_id']) ?>" class="btn btn-outline-info btn-sm" target="_blank">
                                        <i class="bi bi-link-45deg"></i> 查看報價單
                                    </a>
                                </div>
                                <input type="hidden" name="o_q_id" value="<?= $data['o_q_id'] ?>">
                            <?php else: ?>
                                <input type="text" class="form-control" value="無" readonly disabled>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- 商品項目區塊 -->
                <div class="mb-4">
                    <h5 class="border-bottom pb-2 mb-3">
                        <i class="bi bi-cart me-2 text-primary"></i>商品項目 <span class="text-danger">*</span>
                    </h5>

                    <div class="table-responsive">
                        <table class="table" id="itemsTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 35%;">商品分類 / 商品</th>
                                    <th style="width: 10%;">數量</th>
                                    <th style="width: 15%;">單價</th>
                                    <th style="width: 10%;">單位</th>
                                    <th style="width: 10%;">折扣(%)</th>
                                    <th style="width: 12%;">金額</th>
                                    <th style="width: 13%;" class="text-center">操作</th>
                                </tr>
                            </thead>
                            <tbody id="itemsContainer">
                                <?php
                                $items = $data['items'] ?? [[]];
                                if (empty($items)) $items = [[]];
                                foreach ($items as $index => $item):
                                    $shippedQty = $item['oi_shipped_quantity'] ?? 0;
                                    $hasShipped = $shippedQty > 0;
                                    $selectedCategoryId = '';
                                    if (!empty($item['oi_p_id'])) {
                                        foreach ($products as $product) {
                                            if ($product['p_id'] == $item['oi_p_id']) {
                                                $selectedCategoryId = $product['p_pc_id'] ?? '';
                                                break;
                                            }
                                        }
                                    }
                                ?>
                                    <tr class="item-row" data-shipped-qty="<?= $shippedQty ?>">
                                        <td>
                                            <div class="d-flex flex-column flex-lg-row gap-2 align-items-start align-items-lg-center">
                                                <select class="form-select category-select" data-index="<?= $index ?>">
                                                    <option value="">全部分類</option>
                                                    <?php foreach ($productCategories as $category): ?>
                                                        <option value="<?= $category['pc_id'] ?>" <?= ($selectedCategoryId == $category['pc_id']) ? 'selected' : '' ?>>
                                                            <?= esc($category['pc_name']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <select class="form-select product-select" name="items[<?= $index ?>][oi_p_id]" required>
                                                    <option value=""></option>
                                                    <?php foreach ($products as $product): ?>
                                                        <option value="<?= $product['p_id'] ?>"
                                                            data-price="<?= $product['p_standard_price'] ?>"
                                                            data-unit="<?= $product['p_unit'] ?>"
                                                            data-category="<?= $product['p_pc_id'] ?? '' ?>"
                                                            <?= (isset($item['oi_p_id']) && $item['oi_p_id'] == $product['p_id']) ? 'selected' : '' ?>>
                                                            <?= esc($product['p_name']) ?> (<?= esc($product['p_code']) ?>)
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <?php if ($hasShipped): ?>
                                                <small class="text-muted">已出貨：<?= $shippedQty ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control quantity-input" 
                                                name="items[<?= $index ?>][oi_quantity]" 
                                                value="<?= $item['oi_quantity'] ?? 1 ?>" 
                                                min="<?= $shippedQty > 0 ? $shippedQty : 1 ?>" 
                                                required
                                                data-original-qty="<?= $item['oi_quantity'] ?? 1 ?>">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control price-input" name="items[<?= $index ?>][oi_unit_price]" value="<?= $item['oi_unit_price'] ?? 0 ?>" min="0" step="0.01" required>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control unit-display" value="" readonly tabindex="-1">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control discount-input" name="items[<?= $index ?>][oi_discount]" value="<?= $item['oi_discount'] ?? 0 ?>" min="0" max="100" step="0.01">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control amount-display" value="<?= $item['oi_amount'] ?? 0 ?>" readonly tabindex="-1">
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-outline-danger btn-sm remove-item" 
                                                <?= $hasShipped ? 'disabled title="此商品已有出貨記錄，無法刪除"' : '' ?>>
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <button type="button" class="btn btn-success btn-sm" id="addItemBtn">
                        <i class="bi bi-plus-circle me-1"></i>新增商品項目
                    </button>
                </div>

                <!-- 商品項目模板 -->
                <template id="itemRowTemplate">
                    <tr class="item-row">
                        <td>
                            <div class="d-flex flex-column flex-lg-row gap-2 align-items-start align-items-lg-center">
                                <select class="form-select category-select" data-index="__INDEX__">
                                    <option value="">全部分類</option>
                                    <?php foreach ($productCategories as $category): ?>
                                        <option value="<?= $category['pc_id'] ?>">
                                            <?= esc($category['pc_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <select class="form-select product-select" name="items[__INDEX__][oi_p_id]" required>
                                    <option value=""></option>
                                    <?php foreach ($products as $product): ?>
                                        <option value="<?= $product['p_id'] ?>"
                                            data-price="<?= $product['p_standard_price'] ?>"
                                            data-unit="<?= $product['p_unit'] ?>"
                                            data-category="<?= $product['p_pc_id'] ?? '' ?>">
                                            <?= esc($product['p_name']) ?> (<?= esc($product['p_code']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </td>
                        <td>
                            <input type="number" class="form-control quantity-input" name="items[__INDEX__][oi_quantity]" value="1" min="1" required>
                        </td>
                        <td>
                            <input type="number" class="form-control price-input" name="items[__INDEX__][oi_unit_price]" value="0" min="0" step="0.01" required>
                        </td>
                        <td>
                            <input type="text" class="form-control unit-display" value="" readonly tabindex="-1">
                        </td>
                        <td>
                            <input type="number" class="form-control discount-input" name="items[__INDEX__][oi_discount]" value="0" min="0" max="100" step="0.01">
                        </td>
                        <td>
                            <input type="text" class="form-control amount-display" value="0" readonly tabindex="-1">
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-outline-danger btn-sm remove-item">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                </template>

                <!-- 金額計算區塊 -->
                <div class="mb-4">
                    <h5 class="border-bottom pb-2 mb-3">
                        <i class="bi bi-calculator me-2 text-primary"></i>金額計算
                    </h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="notes" class="form-label">備註</label>
                            <textarea
                                class="form-control"
                                id="notes"
                                name="o_notes"
                                rows="4"
                                placeholder="請輸入備註"><?= old('o_notes', $data['o_notes'] ?? '') ?></textarea>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr class="table-primary">
                                    <td class="text-end fw-bold fs-5">訂單總金額：</td>
                                    <td class="text-end fw-bold fs-5 text-primary">
                                        <span id="totalDisplay">NT$ <?= number_format($data['o_total_amount'] ?? 0) ?></span>
                                        <input type="hidden" name="o_total_amount" id="totalInput" value="<?= $data['o_total_amount'] ?? 0 ?>">
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- 表單按鈕 -->
                <div class="d-flex gap-2 justify-content-end">
                    <a href="<?= url_to('OrderController::index') ?>" class="btn btn-outline-secondary">
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
    document.addEventListener('DOMContentLoaded', function() {
        // --- Configuration ---
        const TOM_SELECT_COMMON_CONFIG = {
            plugins: ['clear_button'],
            create: false,
            sortField: {
                field: "text",
                direction: "asc"
            },
            dropdownParent: 'body'
        };

        // --- State ---
        let itemIndex = <?= count($items) ?>;
        const products = <?= json_encode($products) ?>;
        let deliveryAddressManager = null;

        // --- Initialization ---
        initCustomerSelect();
        initProductTomSelect(document.querySelectorAll('.product-select'));
        document.querySelectorAll('.item-row').forEach(row => {
            const categorySelect = row.querySelector('.category-select');
            if (categorySelect && categorySelect.value) {
                applyCategoryFilter(row, true);
            }
        });
        initCalculations();
        updateRemoveButtons();
        initDeliveryAddressSection();

        // --- Event Listeners ---
        bindEvents();

        // --- Functions ---
        function initCustomerSelect() {
            const customerSelect = document.getElementById('customer');
            if (customerSelect) {
                new TomSelect(customerSelect, {
                    ...TOM_SELECT_COMMON_CONFIG,
                    placeholder: '請選擇客戶'
                });
            }
        }

        function initProductTomSelect(elements) {
            elements.forEach(function(element) {
                if (element.tomselect) return;
                new TomSelect(element, {
                    ...TOM_SELECT_COMMON_CONFIG,
                    placeholder: '請選擇商品',
                    onInitialize: function() {
                        this.dropdown_content.style.maxHeight = '250px';
                        // Trigger change to populate unit/price if value exists
                        if (this.getValue()) {
                            const selectedValue = this.getValue();
                            const selectedProduct = products.find(p => p.p_id == selectedValue);
                            if (selectedProduct) {
                                const row = this.wrapper.closest('.item-row');
                                row.querySelector('.unit-display').value = selectedProduct.p_unit || '';
                                // Only set price if input is empty (to avoid overwriting saved values)
                                const priceInput = row.querySelector('.price-input');
                                if (!priceInput.value || priceInput.value == 0) {
                                    priceInput.value = selectedProduct.p_standard_price || 0;
                                }
                            }
                        }
                    }
                });
            });
        }

        function initDeliveryAddressSection() {
            const select = document.getElementById('deliveryAddressSelect');
            if (!select) {
                return;
            }

            deliveryAddressManager = {
                select,
                notice: document.getElementById('deliveryAddressNotice'),
                nameInput: document.getElementById('deliveryAddressName'),
                contactInput: document.getElementById('deliveryContact'),
                phoneInput: document.getElementById('deliveryPhone'),
                addressInput: document.getElementById('deliveryAddressText'),
                endpoint: select.dataset.endpoint,
                cache: new Map(),
                currentAddresses: [],
                selectedCustomerId: select.dataset.initialCustomer || '',
                selectedAddressId: select.dataset.selectedId || '',
                defaultId: null,
            };

            const customerSelect = document.getElementById('customer');
            if (customerSelect) {
                customerSelect.addEventListener('change', function() {
                    deliveryAddressManager.selectedCustomerId = this.value;
                    deliveryAddressManager.selectedAddressId = '';
                    loadDeliveryAddresses(false);
                });
            }

            select.addEventListener('change', function() {
                deliveryAddressManager.selectedAddressId = this.value;
                populateDeliveryAddressDetails();
            });

            if (deliveryAddressManager.selectedCustomerId) {
                loadDeliveryAddresses(true);
            } else {
                disableDeliveryAddressSelect();
                showDeliveryAddressNotice('請先選擇客戶以載入送貨地址', 'info');
            }
        }

        function loadDeliveryAddresses(preserveSelection) {
            if (!deliveryAddressManager || !deliveryAddressManager.endpoint) {
                return;
            }

            const customerId = deliveryAddressManager.selectedCustomerId;
            if (!customerId) {
                disableDeliveryAddressSelect();
                return;
            }

            if (deliveryAddressManager.cache.has(customerId)) {
                const cached = deliveryAddressManager.cache.get(customerId);
                deliveryAddressManager.currentAddresses = cached.data;
                deliveryAddressManager.defaultId = cached.defaultId;
                renderDeliveryAddressOptions(preserveSelection);
                return;
            }

            showDeliveryAddressNotice('正在載入送貨地址...', 'info');

            fetch(`${deliveryAddressManager.endpoint}/${customerId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error();
                    }
                    return response.json();
                })
                .then(result => {
                    if (!result.success) {
                        throw new Error(result.message || '載入送貨地址失敗');
                    }
                    const data = result.data || [];
                    const defaultId = result.defaultId || null;
                    deliveryAddressManager.cache.set(customerId, { data, defaultId });
                    deliveryAddressManager.currentAddresses = data;
                    deliveryAddressManager.defaultId = defaultId;
                    renderDeliveryAddressOptions(preserveSelection);
                })
                .catch(() => {
                    disableDeliveryAddressSelect();
                    showDeliveryAddressNotice('載入送貨地址失敗，請重新選擇客戶或稍後再試', 'danger');
                });
        }

        function renderDeliveryAddressOptions(preserveSelection) {
            if (!deliveryAddressManager) {
                return;
            }

            const select = deliveryAddressManager.select;
            const addresses = deliveryAddressManager.currentAddresses;
            select.innerHTML = '<option value=""></option>';

            if (!addresses.length) {
                select.setAttribute('disabled', 'disabled');
                deliveryAddressManager.selectedAddressId = '';
                populateDeliveryAddressDetails();
                showDeliveryAddressNotice('此客戶尚未設定送貨地址，請至客戶管理新增', 'warning');
                return;
            }

            select.removeAttribute('disabled');
            addresses.forEach(address => {
                const option = document.createElement('option');
                option.value = address.cda_id;
                const label = address.cda_name ? `${address.cda_name} - ${address.cda_address}` : address.cda_address;
                option.textContent = label;
                select.appendChild(option);
            });

            let targetId = preserveSelection ? deliveryAddressManager.selectedAddressId : null;
            if (targetId && !addresses.some(address => String(address.cda_id) === String(targetId))) {
                targetId = null;
            }

            if (!targetId && deliveryAddressManager.defaultId) {
                targetId = deliveryAddressManager.defaultId;
            }

            if (!targetId) {
                targetId = addresses[0].cda_id;
            }

            deliveryAddressManager.selectedAddressId = targetId ? String(targetId) : '';
            select.value = deliveryAddressManager.selectedAddressId;
            hideDeliveryAddressNotice();
            populateDeliveryAddressDetails();
        }

        function populateDeliveryAddressDetails() {
            if (!deliveryAddressManager) {
                return;
            }

            const selected = deliveryAddressManager.currentAddresses.find(address => String(address.cda_id) === String(deliveryAddressManager.selectedAddressId));

            deliveryAddressManager.nameInput.value = selected ? (selected.cda_name || '') : '';
            deliveryAddressManager.contactInput.value = selected ? (selected.cda_contact_person || '') : '';
            deliveryAddressManager.phoneInput.value = selected ? (selected.cda_phone || '') : '';
            deliveryAddressManager.addressInput.value = selected ? (selected.cda_address || '') : '';
        }

        function showDeliveryAddressNotice(message, type = 'warning') {
            if (!deliveryAddressManager || !deliveryAddressManager.notice) {
                return;
            }

            const notice = deliveryAddressManager.notice;
            notice.textContent = message;
            notice.classList.remove('d-none', 'alert-warning', 'alert-info', 'alert-danger', 'alert-success');
            notice.classList.add(`alert-${type}`);
        }

        function hideDeliveryAddressNotice() {
            if (!deliveryAddressManager || !deliveryAddressManager.notice) {
                return;
            }
            deliveryAddressManager.notice.classList.add('d-none');
        }

        function disableDeliveryAddressSelect() {
            if (!deliveryAddressManager) {
                return;
            }

            deliveryAddressManager.select.value = '';
            deliveryAddressManager.select.setAttribute('disabled', 'disabled');
            deliveryAddressManager.currentAddresses = [];
            deliveryAddressManager.selectedAddressId = '';
            populateDeliveryAddressDetails();
        }

        function initCalculations() {
            document.querySelectorAll('.item-row').forEach(row => {
                // Initialize unit display
                const select = row.querySelector('.product-select');
                if (select && select.value) {
                    // If using TomSelect, we need to access the option via API or DOM if not initialized yet?
                    // Actually, the select element still has options.
                    const option = select.options[select.selectedIndex];
                    if (option) {
                        row.querySelector('.unit-display').value = option.dataset.unit || '';
                    }
                }
                calculateItemAmount(row);
            });

            // Calculate total after initializing all items
            calculateTotal();
        }

        function bindEvents() {
            document.getElementById('addItemBtn').addEventListener('click', handleAddItem);

            const itemsContainer = document.getElementById('itemsContainer');
            itemsContainer.addEventListener('click', handleRemoveItem);
            itemsContainer.addEventListener('change', handleItemChange);
            itemsContainer.addEventListener('input', handleItemInput);

            document.getElementById('orderForm').addEventListener('submit', handleFormSubmit);
        }

        function handleItemInput(e) {
            if (e.target.classList.contains('quantity-input') ||
                e.target.classList.contains('price-input') ||
                e.target.classList.contains('discount-input')) {
                const row = e.target.closest('.item-row');
                
                // 驗證數量不能小於已出貨數量
                if (e.target.classList.contains('quantity-input')) {
                    const shippedQty = parseFloat(row.dataset.shippedQty) || 0;
                    const newQty = parseFloat(e.target.value) || 0;
                    
                    if (shippedQty > 0 && newQty < shippedQty) {
                        e.target.setCustomValidity(`訂購數量不能小於已出貨數量 (${shippedQty})`);
                        e.target.classList.add('is-invalid');
                    } else {
                        e.target.setCustomValidity('');
                        e.target.classList.remove('is-invalid');
                    }
                }
                
                calculateItemAmount(row);
            }
        }

        function handleItemChange(e) {
            if (e.target.classList.contains('category-select')) {
                const row = e.target.closest('.item-row');
                applyCategoryFilter(row, true);
                return;
            }

            if (e.target.classList.contains('product-select')) {
                const selectedValue = e.target.value;
                const row = e.target.closest('.item-row');

                // Find the selected product from the products array
                const selectedProduct = products.find(p => p.p_id == selectedValue);
                if (selectedProduct) {
                    row.querySelector('.price-input').value = selectedProduct.p_standard_price || 0;
                    row.querySelector('.unit-display').value = selectedProduct.p_unit || '';
                } else {
                    row.querySelector('.price-input').value = 0;
                    row.querySelector('.unit-display').value = '';
                }

                calculateItemAmount(row);
            }
        }

        function handleAddItem() {
            const container = document.getElementById('itemsContainer');
            const newIndex = itemIndex++;
            const template = document.getElementById('itemRowTemplate');
            const clone = template.content.cloneNode(true);

            const tempDiv = document.createElement('div');
            tempDiv.appendChild(clone);
            let html = tempDiv.innerHTML.replace(/__INDEX__/g, newIndex);

            container.insertAdjacentHTML('beforeend', html);

            const newRow = container.lastElementChild;
            const productSelect = newRow.querySelector('.product-select');
            initProductTomSelect([productSelect]);
            const categorySelect = newRow.querySelector('.category-select');
            if (categorySelect && categorySelect.value) {
                applyCategoryFilter(newRow, true);
            }
            updateRemoveButtons();
        }

        function handleRemoveItem(e) {
            if (e.target.closest('.remove-item')) {
                const btn = e.target.closest('.remove-item');
                
                // 檢查按鈕是否被禁用
                if (btn.disabled) {
                    alert('此商品已有出貨記錄，無法刪除');
                    return;
                }
                
                const row = e.target.closest('.item-row');
                if (document.querySelectorAll('.item-row').length <= 1) {
                    alert('至少需要保留一個商品項目');
                    return;
                }

                const select = row.querySelector('.product-select');
                if (select && select.tomselect) select.tomselect.destroy();

                row.remove();
                calculateTotal();
                updateRemoveButtons();
            }
        }

        function applyCategoryFilter(row, preserveValue = true) {
            const categorySelect = row.querySelector('.category-select');
            const categoryId = categorySelect ? categorySelect.value : '';
            const productSelect = row.querySelector('.product-select');

            if (!productSelect) {
                return;
            }

            const tomInstance = productSelect.tomselect ?? null;
            const currentValue = tomInstance ? tomInstance.getValue() : productSelect.value;
            const filteredProducts = getProductsByCategory(categoryId);
            const shouldPreserveValue = preserveValue && currentValue && filteredProducts.some(product => String(product.p_id) === String(currentValue));

            if (tomInstance) {
                tomInstance.destroy();
                delete productSelect.tomselect;
            }

            productSelect.innerHTML = '<option value=""></option>';
            filteredProducts.forEach(product => {
                const option = document.createElement('option');
                option.value = product.p_id;
                option.dataset.price = product.p_standard_price;
                option.dataset.unit = product.p_unit || '';
                option.dataset.category = product.p_pc_id || '';
                option.textContent = `${product.p_name} (${product.p_code})`;
                productSelect.appendChild(option);
            });

            initProductTomSelect([productSelect]);

            if (shouldPreserveValue && productSelect.tomselect) {
                productSelect.tomselect.setValue(currentValue, true);
            } else if (productSelect.tomselect) {
                productSelect.tomselect.clear();
                row.querySelector('.price-input').value = 0;
                row.querySelector('.unit-display').value = '';
                calculateItemAmount(row);
            }
        }

        function getProductsByCategory(categoryId) {
            if (!categoryId) {
                return products;
            }

            return products.filter(product => String(product.p_pc_id || '') === String(categoryId));
        }

        function calculateItemAmount(row) {
            const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
            const unitPrice = parseFloat(row.querySelector('.price-input').value) || 0;
            const discount = parseFloat(row.querySelector('.discount-input').value) || 0;

            const amount = quantity * unitPrice * (1 - discount / 100);
            row.querySelector('.amount-display').value = amount.toFixed(0);

            calculateTotal();
        }

        function calculateTotal() {
            let total = 0;
            document.querySelectorAll('.item-row').forEach(row => {
                total += parseFloat(row.querySelector('.amount-display').value) || 0;
            });

            document.getElementById('totalDisplay').textContent = 'NT$ ' + total.toLocaleString('zh-TW', {
                minimumFractionDigits: 0
            });
            document.getElementById('totalInput').value = total;
        }

        function updateRemoveButtons() {
            const items = document.querySelectorAll('.item-row');
            const removeButtons = document.querySelectorAll('.remove-item');
            const disabled = items.length === 1;
            removeButtons.forEach(btn => btn.disabled = disabled);
        }

        function handleFormSubmit(e) {
            const submitBtn = document.getElementById('submitBtn');
            if (document.querySelectorAll('.item-row').length === 0) {
                e.preventDefault();
                alert('至少需要新增一個商品項目');
                return false;
            }

            if (!this.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
                this.classList.add('was-validated');
                return false;
            }

            if (submitBtn.disabled) {
                e.preventDefault();
                return false;
            }

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>儲存中...';
        }
    });
</script>

<?= $this->endSection() ?>