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

<div class="container-fluid mt-4">
    <!-- 頁面標題 -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>
            <i class="bi bi-<?= $isEdit ? 'pencil-square' : 'plus-circle' ?> me-2"></i>
            <?= $isEdit ? '編輯' : '新增' ?>報價單
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
            <form id="quoteForm" action="<?= url_to('QuoteController::save') ?>" method="post" novalidate>
                <?php if ($isEdit): ?>
                    <input type="hidden" name="q_id" value="<?= $data['q_id'] ?? old('q_id') ?>">
                <?php endif; ?>

                <!-- 基本資料區塊 -->
                <div class="mb-4">
                    <h5 class="border-bottom pb-2 mb-3">
                        <i class="bi bi-file-earmark-text me-2 text-primary"></i>基本資料
                    </h5>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="quoteNumber" class="form-label">報價單號</label>
                            <input
                                type="text"
                                class="form-control <?= getFieldClass('q_number') ?>"
                                id="quoteNumber"
                                name="q_number"
                                value="<?= old('q_number', $data['q_number'] ?? $quoteNumber ?? '') ?>"
                                required
                                readonly>
                            <?= showFieldError('q_number') ?>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="quoteDate" class="form-label">
                                報價日期 <span class="text-danger">*</span>
                            </label>
                            <input
                                type="date"
                                class="form-control <?= getFieldClass('q_date') ?>"
                                id="quoteDate"
                                name="q_date"
                                value="<?= old('q_date', $data['q_date'] ?? date('Y-m-d')) ?>"
                                required>
                            <?= showFieldError('q_date') ?>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="validDate" class="form-label">有效日期</label>
                            <input
                                type="date"
                                class="form-control <?= getFieldClass('q_valid_date') ?>"
                                id="validDate"
                                name="q_valid_date"
                                value="<?= old('q_valid_date', $data['q_valid_date'] ?? '') ?>">
                            <?= showFieldError('q_valid_date') ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="customer" class="form-label">
                                客戶 <span class="text-danger">*</span>
                            </label>
                            <select
                                class="form-select <?= getFieldClass('q_c_id') ?>"
                                id="customer"
                                name="q_c_id"
                                required>
                                <option value=""></option>
                                <?php foreach ($customers as $customer): ?>
                                    <option value="<?= $customer['c_id'] ?>"
                                        <?= (old('q_c_id', $data['q_c_id'] ?? '') == $customer['c_id']) ? 'selected' : '' ?>>
                                        <?= esc($customer['c_name']) ?> - <?= esc($customer['c_contact_person']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?= showFieldError('q_c_id') ?>
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
                                    <th style="width: 30%;">商品</th>
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
                                    echo view('components/quote_item_row', [
                                        'index' => $index,
                                        'item' => $item,
                                        'products' => $products,
                                        'isTemplate' => false
                                    ]);
                                endforeach;
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <button type="button" class="btn btn-success btn-sm" id="addItemBtn">
                        <i class="bi bi-plus-circle me-1"></i>新增商品項目
                    </button>
                </div>

                <!-- 商品項目模板（供 JavaScript 使用） -->
                <template id="itemRowTemplate">
                    <?= view('components/quote_item_row', [
                        'index' => '__INDEX__',
                        'item' => [],
                        'products' => $products,
                        'isTemplate' => true
                    ]) ?>
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
                                name="q_notes"
                                rows="4"
                                placeholder="請輸入備註"><?= old('q_notes', $data['q_notes'] ?? '') ?></textarea>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <td class="text-end fw-bold">小計：</td>
                                    <td class="text-end">
                                        <span id="subtotalDisplay">NT$ 0</span>
                                        <input type="hidden" name="q_subtotal" id="subtotalInput" value="0">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-end">
                                        <label for="discount" class="form-label mb-0">整單折扣(%)：</label>
                                    </td>
                                    <td class="text-end">
                                        <input type="number" class="form-control form-control-sm text-end"
                                            id="discount" name="q_discount"
                                            value="<?= old('q_discount', $data['q_discount'] ?? 0) ?>"
                                            min="0" max="100" step="0.01">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-end">
                                        <label for="taxRate" class="form-label mb-0">稅率(%)：</label>
                                    </td>
                                    <td class="text-end">
                                        <input type="number" class="form-control form-control-sm text-end"
                                            id="taxRate" name="q_tax_rate"
                                            value="<?= old('q_tax_rate', $data['q_tax_rate'] ?? 5) ?>"
                                            min="0" max="100" step="0.01">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-end fw-bold">稅額：</td>
                                    <td class="text-end">
                                        <span id="taxDisplay">NT$ 0</span>
                                        <input type="hidden" name="q_tax_amount" id="taxInput" value="0">
                                    </td>
                                </tr>
                                <tr class="table-primary">
                                    <td class="text-end fw-bold fs-5">總金額：</td>
                                    <td class="text-end fw-bold fs-5 text-primary">
                                        <span id="totalDisplay">NT$ 0</span>
                                        <input type="hidden" name="q_total_amount" id="totalInput" value="0">
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- 必填欄位說明 -->
                <div class="alert alert-info py-2 mb-4">
                    <i class="bi bi-info-circle me-2"></i>
                    標示 <span class="text-danger">*</span> 為必填欄位
                </div>

                <!-- 表單按鈕 -->
                <div class="d-flex gap-2 justify-content-end">
                    <a href="<?= url_to('QuoteController::index') ?>" class="btn btn-outline-secondary">
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
            dropdownParent: 'body' // Fix for dropdown being clipped in table
        };

        // --- State ---
        let itemIndex = <?= count($items) ?>;
        const products = <?= json_encode($products) ?>;

        // --- Initialization ---
        initCustomerSelect();
        initProductTomSelect(document.querySelectorAll('.product-select'));
        initCalculations();
        updateRemoveButtons();

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
                if (element.tomselect) return; // Skip if already initialized

                new TomSelect(element, {
                    ...TOM_SELECT_COMMON_CONFIG,
                    placeholder: '請選擇商品',
                    onInitialize: function() {
                        // Ensure dropdown content has a reasonable max height
                        this.dropdown_content.style.maxHeight = '250px';
                    }
                });
            });
        }

        function initCalculations() {
            document.querySelectorAll('.item-row').forEach(row => {
                calculateItemAmount(row);
            });
        }

        function bindEvents() {
            // Add Item Button
            document.getElementById('addItemBtn').addEventListener('click', handleAddItem);

            // Items Container (Delegated events for Remove, Change, and Input)
            const itemsContainer = document.getElementById('itemsContainer');
            itemsContainer.addEventListener('click', handleRemoveItem);
            itemsContainer.addEventListener('change', handleItemChange);
            itemsContainer.addEventListener('input', handleItemInput);

            // Global Discount and Tax
            document.getElementById('discount').addEventListener('input', calculateTotal);
            document.getElementById('taxRate').addEventListener('input', calculateTotal);

            // Form Submit
            document.getElementById('quoteForm').addEventListener('submit', handleFormSubmit);
        }

        function handleAddItem() {
            const container = document.getElementById('itemsContainer');
            const newIndex = itemIndex++;

            // Clone template
            const template = document.getElementById('itemRowTemplate');
            const clone = template.content.cloneNode(true);

            // Replace index placeholder
            const tempDiv = document.createElement('div');
            tempDiv.appendChild(clone);
            let html = tempDiv.innerHTML;
            html = html.replace(/__INDEX__/g, newIndex);

            // Insert and initialize
            container.insertAdjacentHTML('beforeend', html);

            const newRow = container.lastElementChild;
            const newSelect = newRow.querySelector('.product-select');
            initProductTomSelect([newSelect]);

            updateRemoveButtons();
        }

        function handleRemoveItem(e) {
            if (e.target.closest('.remove-item')) {
                const row = e.target.closest('.item-row');
                const itemCount = document.querySelectorAll('.item-row').length;

                if (itemCount <= 1) {
                    alert('至少需要保留一個商品項目');
                    return;
                }

                // Destroy Tom Select instance before removing to prevent memory leaks
                const select = row.querySelector('.product-select');
                if (select && select.tomselect) {
                    select.tomselect.destroy();
                }

                row.remove();
                calculateTotal();
                updateRemoveButtons();
            }
        }

        function handleItemInput(e) {
            // Real-time calculation for inputs
            if (e.target.classList.contains('quantity-input') ||
                e.target.classList.contains('price-input') ||
                e.target.classList.contains('discount-input')) {
                const row = e.target.closest('.item-row');
                calculateItemAmount(row);
            }
        }

        function handleItemChange(e) {
            // Product selection change
            if (e.target.classList.contains('product-select')) {
                const selectedOption = e.target.options[e.target.selectedIndex];
                const price = selectedOption.dataset.price || 0;
                const unit = selectedOption.dataset.unit || '';
                const row = e.target.closest('.item-row');

                row.querySelector('.price-input').value = price;
                row.querySelector('.unit-display').value = unit;
                calculateItemAmount(row);
            }
        }

        function handleFormSubmit(e) {
            const submitBtn = document.getElementById('submitBtn');

            // Check items count
            const items = document.querySelectorAll('.item-row');
            if (items.length === 0) {
                e.preventDefault();
                alert('至少需要新增一個商品項目');
                return false;
            }

            // Form validation
            if (!this.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
                this.classList.add('was-validated');
                return false;
            }

            // Prevent double submit
            if (submitBtn.disabled) {
                e.preventDefault();
                return false;
            }

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>儲存中...';
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
            let subtotal = 0;
            document.querySelectorAll('.item-row').forEach(row => {
                const amount = parseFloat(row.querySelector('.amount-display').value) || 0;
                subtotal += amount;
            });

            const discount = parseFloat(document.getElementById('discount').value) || 0;
            const taxRate = parseFloat(document.getElementById('taxRate').value) || 0;

            const discountedSubtotal = subtotal * (1 - discount / 100);
            const taxAmount = discountedSubtotal * (taxRate / 100);
            const totalAmount = discountedSubtotal + taxAmount;

            document.getElementById('subtotalDisplay').textContent = 'NT$ ' + subtotal.toLocaleString('zh-TW', {
                minimumFractionDigits: 0
            });
            document.getElementById('subtotalInput').value = subtotal;

            document.getElementById('taxDisplay').textContent = 'NT$ ' + taxAmount.toLocaleString('zh-TW', {
                minimumFractionDigits: 0
            });
            document.getElementById('taxInput').value = taxAmount;

            document.getElementById('totalDisplay').textContent = 'NT$ ' + totalAmount.toLocaleString('zh-TW', {
                minimumFractionDigits: 0
            });
            document.getElementById('totalInput').value = totalAmount;
        }

        function updateRemoveButtons() {
            const items = document.querySelectorAll('.item-row');
            const removeButtons = document.querySelectorAll('.remove-item');

            if (items.length === 1) {
                removeButtons.forEach(btn => btn.disabled = true);
            } else {
                removeButtons.forEach(btn => btn.disabled = false);
            }
        }
    });
</script>

<?= $this->endSection() ?>