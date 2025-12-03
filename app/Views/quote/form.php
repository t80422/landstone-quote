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
$selectedCustomerId = old('q_c_id', $data['q_c_id'] ?? '');
$selectedDeliveryAddressId = old('q_cda_id', $data['q_cda_id'] ?? '');

$productCategories = $productCategories ?? [];
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
                        <!-- 報價日期 -->
                        <div class="col-md-4 mb-3">
                            <label for="quoteDate" class="form-label">
                                報價日期 <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input
                                    type="date"
                                    class="form-control <?= getFieldClass('q_date') ?>"
                                    id="quoteDate"
                                    name="q_date"
                                    value="<?= old('q_date', $data['q_date'] ?? date('Y-m-d')) ?>"
                                    required>
                            </div>
                            <?= showFieldError('q_date') ?>
                        </div>
                        <!-- 有效日期 -->
                        <div class="col-md-4 mb-3">
                            <label for="validDate" class="form-label">有效日期</label>
                            <div class="input-group">
                                <input
                                    type="date"
                                    class="form-control <?= getFieldClass('q_valid_date') ?>"
                                    id="validDate"
                                    name="q_valid_date"
                                    value="<?= old('q_valid_date', $data['q_valid_date'] ?? '') ?>">
                            </div>
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

                    <div class="row align-items-start mb-3">
                        <div class="col-md-6 mb-3">
                            <label for="deliveryAddressSelect" class="form-label">
                                送貨地址 <span class="text-danger">*</span>
                            </label>
                            <select
                                class="form-select <?= getFieldClass('q_cda_id') ?>"
                                id="deliveryAddressSelect"
                                name="q_cda_id"
                                required
                                data-endpoint="<?= base_url('customer/delivery-addresses') ?>"
                                data-initial-customer="<?= esc($selectedCustomerId) ?>"
                                data-selected-id="<?= esc($selectedDeliveryAddressId) ?>">
                                <option value=""></option>
                            </select>
                            <?= showFieldError('q_cda_id') ?>
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
                                    echo view('components/quote_item_row', [
                                        'index' => $index,
                                        'item' => $item,
                                        'products' => $products,
                                        'productCategories' => $productCategories,
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
                        'productCategories' => $productCategories,
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
            const notice = deliveryAddressManager.notice;
            notice.classList.add('d-none');
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
            const categorySelect = newRow.querySelector('.category-select');
            if (categorySelect && categorySelect.value) {
                applyCategoryFilter(newRow, true);
            }

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
            if (e.target.classList.contains('category-select')) {
                const row = e.target.closest('.item-row');
                applyCategoryFilter(row, true);
                return;
            }

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
                option.textContent = `${product.p_code} - ${product.p_name}`;
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