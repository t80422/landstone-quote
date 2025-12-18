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
?>

<?= $this->extend('_layout') ?>
<?= $this->section('content') ?>

<style>
    /* 商品項目表格優化 */
    #itemsTable thead th {
        background-color: #f8f9fa;
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
        white-space: nowrap;
        padding: 0.75rem 0.5rem;
    }

    #itemsTable tbody td {
        padding: 0.5rem;
        vertical-align: middle;
    }

    .item-row {
        transition: background-color 0.2s;
    }

    .item-row:hover {
        background-color: #f8f9fa;
    }

    /* 圖片預覽優化 */
    .item-image-preview {
        transition: transform 0.2s;
    }

    .item-image-preview:hover {
        transform: scale(1.05);
    }

    /* 表單控制項優化 */
    .form-control-sm,
    .form-select-sm {
        font-size: 0.875rem;
    }

    .small {
        font-size: 0.875rem;
    }

    /* 金額顯示強調 */
    .amount-display {
        color: #0d6efd;
    }

    /* 必填欄位標示 */
    .form-label .text-danger {
        margin-left: 2px;
    }

    /* 按鈕優化 */
    .remove-item {
        transition: all 0.2s;
    }

    .remove-item:hover {
        transform: scale(1.1);
    }

    /* 表格固定表頭 */
    .table-responsive {
        max-height: 600px;
        overflow-y: auto;
    }

    .sticky-top {
        position: sticky;
        top: 0;
        z-index: 10;
    }
</style>

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
                        <!-- 報價單號 -->
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
                                    value="<?php
                                            $validDate = old('q_valid_date', $data['q_valid_date'] ?? '');
                                            if (empty($validDate) && !$isEdit) {
                                                // 新增模式且沒有有效日期時，預設為報價日期+15天
                                                $quoteDate = old('q_date', $data['q_date'] ?? date('Y-m-d'));
                                                $quoteDateObj = new DateTime($quoteDate);
                                                $quoteDateObj->modify('+15 days');
                                                $validDate = $quoteDateObj->format('Y-m-d');
                                            }
                                            echo $validDate;
                                            ?>">
                            </div>
                            <?= showFieldError('q_valid_date') ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
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
                                        <?= esc($customer['c_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?= showFieldError('q_c_id') ?>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="contactSelect" class="form-label">
                                聯絡人
                            </label>
                            <select
                                class="form-select <?= getFieldClass('q_cc_id') ?>"
                                id="contactSelect"
                                name="q_cc_id"
                                data-endpoint="<?= base_url('customer/contacts') ?>"
                                data-initial-customer="<?= esc($data['q_c_id'] ?? '') ?>"
                                data-selected-id="<?= esc(old('q_cc_id', $data['q_cc_id'] ?? '')) ?>">
                                <option value="">請先選擇客戶</option>
                            </select>
                            <?= showFieldError('q_cc_id') ?>
                            <div class="form-text" id="contactInfo" data-placeholder="電話 / Email"></div>
                        </div>
                    </div>

                    <!-- 送貨地址區塊 -->
                    <div class="row">
                        <div class="col-12">
                            <?= view('components/delivery_address_selector', [
                                'deliveryCity' => $data['q_delivery_city'] ?? old('q_delivery_city'),
                                'deliveryAddress' => $data['q_delivery_address'] ?? old('q_delivery_address'),
                                'prefix' => 'q'
                            ]) ?>
                        </div>
                    </div>

                </div>

                <!-- 商品項目區塊 -->
                <div class="mb-4">
                    <h5 class="border-bottom pb-2 mb-3">
                        <i class="bi bi-cart me-2 text-primary"></i>商品項目 <span class="text-danger">*</span>
                    </h5>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="itemsTable">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th style="width: 10%;" class="text-center">
                                        <i class="bi bi-image me-1"></i>圖片
                                    </th>
                                    <th style="width: 20%;">
                                        <i class="bi bi-box-seam me-1"></i>商品分類 / 商品
                                    </th>
                                    <th style="width: 10%;" class="small">
                                        <i class="bi bi-truck me-1"></i>供應商
                                    </th>
                                    <th style="width: 10%;" class="small">
                                        <i class="bi bi-paint-bucket me-1"></i>顏色/花色
                                    </th>
                                    <th style="width: 10%;" class="small">
                                        <i class="bi bi-rulers me-1"></i>尺寸
                                    </th>
                                    <th style="width: 7%;" class="text-center">
                                        <i class="bi bi-123 me-1"></i>數量
                                    </th>
                                    <th style="width: 10%;" class="text-end">
                                        <i class="bi bi-currency-dollar me-1"></i>單價
                                    </th>
                                    <th style="width: 7%;" class="text-center small">
                                        <i class="bi bi-percent me-1"></i>折扣
                                    </th>
                                    <th style="width: 10%;" class="text-end">
                                        <i class="bi bi-calculator me-1"></i>金額
                                    </th>
                                    <th style="width: 4%;" class="text-center">
                                        <i class="bi bi-gear"></i>
                                    </th>
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
                                    <td class="text-end">
                                        <label for="shippingFee" class="form-label mb-0">運費：</label>
                                    </td>
                                    <td class="text-end">
                                        <input type="number" class="form-control form-control-sm text-end"
                                            id="shippingFee" name="q_shipping_fee"
                                            value="<?= old('q_shipping_fee', $data['q_shipping_fee'] ?? 0) ?>"
                                            min="0" step="1">
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
        document.querySelectorAll('.item-row').forEach(row => {
            const categorySelect = row.querySelector('.category-select');
            if (categorySelect && categorySelect.value) {
                applyCategoryFilter(row, true);
            }
            initVariantOptions(row, true);
        });
        initCalculations();
        updateRemoveButtons();
        // 送貨地址管理器已在 component 中自動初始化
        initContacts();
        initValidDateCalculation();

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

        function initContacts() {
            const customerSelect = document.getElementById('customer');
            const contactSelect = document.getElementById('contactSelect');
            const contactInfo = document.getElementById('contactInfo');
            const endpoint = contactSelect ? contactSelect.dataset.endpoint : '';
            const initialCustomerId = contactSelect ? contactSelect.dataset.initialCustomer : '';
            const initialContactId = contactSelect ? contactSelect.dataset.selectedId : '';

            if (!contactSelect || !customerSelect || !endpoint) {
                return;
            }

            const tomConfig = {
                ...TOM_SELECT_COMMON_CONFIG,
                placeholder: '請選擇聯絡人'
            };

            const contactSelectInstance = new TomSelect(contactSelect, tomConfig);

            customerSelect.addEventListener('change', function() {
                loadContacts(this.value, contactSelectInstance, contactInfo, endpoint);
            });

            if (initialCustomerId) {
                loadContacts(initialCustomerId, contactSelectInstance, contactInfo, endpoint, initialContactId, true);
            }
        }

        function initValidDateCalculation() {
            const quoteDateInput = document.getElementById('quoteDate');
            const validDateInput = document.getElementById('validDate');

            if (!quoteDateInput || !validDateInput) {
                return;
            }

            // 當報價日期變更時，自動計算有效日期為報價日期+15天
            quoteDateInput.addEventListener('change', function() {
                const quoteDate = new Date(this.value);
                if (!isNaN(quoteDate.getTime())) {
                    // 加上15天
                    quoteDate.setDate(quoteDate.getDate() + 15);
                    // 格式化為YYYY-MM-DD
                    const validDateStr = quoteDate.toISOString().split('T')[0];
                    validDateInput.value = validDateStr;
                }
            });
        }

        function loadContacts(customerId, selectInstance, infoDom, endpoint, selectedId = '', preserveValue = false) {
            if (!customerId) {
                selectInstance.clearOptions();
                selectInstance.addOption({
                    value: '',
                    text: '請先選擇客戶'
                });
                selectInstance.setValue('');
                infoDom.textContent = infoDom.dataset.placeholder || '';
                return;
            }

            fetch(`${endpoint}/${customerId}`)
                .then(resp => resp.json())
                .then(result => {
                    selectInstance.clearOptions();

                    if (!result.success || !result.data || result.data.length === 0) {
                        selectInstance.addOption({
                            value: '',
                            text: '無聯絡人資料'
                        });
                        selectInstance.setValue('');
                        infoDom.textContent = '無聯絡人資料';
                        return;
                    }

                    const contacts = result.data;
                    contacts.forEach(c => {
                        const label = c.cc_phone ? `${c.cc_name} (${c.cc_phone})` : c.cc_name;
                        selectInstance.addOption({
                            value: c.cc_id,
                            text: label,
                            phone: c.cc_phone,
                            email: c.cc_email
                        });
                    });

                    let targetId = preserveValue ? selectInstance.getValue() : selectedId;
                    if (!targetId || !contacts.some(c => String(c.cc_id) === String(targetId))) {
                        targetId = contacts[0].cc_id;
                    }

                    selectInstance.setValue(targetId);

                    const selected = contacts.find(c => String(c.cc_id) === String(targetId));
                    if (selected) {
                        infoDom.textContent = `${selected.cc_email || ''}`.trim();
                    } else {
                        infoDom.textContent = infoDom.dataset.placeholder || '';
                    }

                    selectInstance.on('change', function(value) {
                        const sel = contacts.find(c => String(c.cc_id) === String(value));
                        if (sel) {
                            infoDom.textContent = `${sel.cc_email || ''}`.trim();
                        } else {
                            infoDom.textContent = infoDom.dataset.placeholder || '';
                        }
                    });
                })
                .catch(() => {
                    selectInstance.clearOptions();
                    selectInstance.addOption({
                        value: '',
                        text: '載入失敗'
                    });
                    selectInstance.setValue('');
                    infoDom.textContent = '載入聯絡人失敗';
                });
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
                    },
                    onChange: function(value) {
                        // 當商品選擇變更時，更新價格和變體選項
                        const row = element.closest('.item-row');
                        if (!row) return;

                        if (value) {
                            const selectedProduct = products.find(p => p.p_id == value);
                            if (selectedProduct) {
                                row.querySelector('.price-input').value = selectedProduct.p_standard_price || 0;
                            } else {
                                row.querySelector('.price-input').value = 0;
                            }
                        } else {
                            row.querySelector('.price-input').value = 0;
                        }

                        initVariantOptions(row, false);
                        calculateItemAmount(row);
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

            // Global Discount, Tax, and Shipping Fee
            document.getElementById('discount').addEventListener('input', calculateTotal);
            document.getElementById('taxRate').addEventListener('input', calculateTotal);
            document.getElementById('shippingFee').addEventListener('input', calculateTotal);

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
            initVariantOptions(newRow, true);

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
                const row = e.target.closest('.item-row');

                row.querySelector('.price-input').value = price;
                initVariantOptions(row, false);
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
                calculateItemAmount(row);
            }
            initVariantOptions(row, !preserveValue);
        }

        function initVariantOptions(row, preserveExisting) {
            const productSelect = row.querySelector('.product-select');
            const productId = productSelect ? productSelect.value : '';
            const product = products.find(p => String(p.p_id) === String(productId));

            const supplierSelect = row.querySelector('.supplier-select');
            const colorSelect = row.querySelector('.color-select');
            const sizeSelect = row.querySelector('.size-select');
            const imagePreview = row.querySelector('.item-image-preview');

            const savedSupplier = row.dataset.selectedSupplier || '';
            const savedColor = row.dataset.selectedColor || '';
            const savedSize = row.dataset.selectedSize || '';

            const setOptions = (selectEl, values, saved) => {
                if (!selectEl) return;
                const current = preserveExisting ? (selectEl.value || saved) : saved;
                selectEl.innerHTML = '<option value=""></option>';
                values.forEach(v => {
                    const opt = document.createElement('option');
                    opt.value = v;
                    opt.textContent = v;
                    selectEl.appendChild(opt);
                });
                if (current && values.includes(current)) {
                    selectEl.value = current;
                }
            };

            const splitValues = (str) => {
                if (!str) return [];
                return str.split('、').map(s => s.trim()).filter(Boolean);
            };

            if (product) {
                setOptions(supplierSelect, splitValues(product.p_supplier), preserveExisting ? (supplierSelect ? supplierSelect.value : '') : savedSupplier);
                setOptions(colorSelect, splitValues(product.p_color), preserveExisting ? (colorSelect ? colorSelect.value : '') : savedColor);
                setOptions(sizeSelect, splitValues(product.p_size), preserveExisting ? (sizeSelect ? sizeSelect.value : '') : savedSize);

                if (imagePreview) {
                    const placeholder = imagePreview.dataset.placeholder || '';
                    // p_image 已包含完整相對路徑（如：uploads/products/xxx.jpg）
                    const imageSrc = product.p_image ? '<?= base_url() ?>' + product.p_image : placeholder;
                    imagePreview.src = imageSrc;
                }
            } else {
                setOptions(supplierSelect, [], '');
                setOptions(styleSelect, [], '');
                setOptions(colorSelect, [], '');
                setOptions(sizeSelect, [], '');
                if (imagePreview) {
                    const placeholder = imagePreview.dataset.placeholder || '';
                    imagePreview.src = placeholder;
                }
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
            // 1. 計算商品小計
            let subtotal = 0;
            document.querySelectorAll('.item-row').forEach(row => {
                const amount = parseFloat(row.querySelector('.amount-display').value) || 0;
                subtotal += amount;
            });

            const discount = parseFloat(document.getElementById('discount').value) || 0;
            const taxRate = parseFloat(document.getElementById('taxRate').value) || 0;
            const shippingFee = parseInt(document.getElementById('shippingFee').value) || 0;

            // 2. 折扣後金額 = 商品小計 × (1 - 整單折扣%)
            const discountedSubtotal = subtotal * (1 - discount / 100);

            // 3. 加運費後 = 折扣後金額 + 運費
            const amountWithShipping = discountedSubtotal + shippingFee;

            // 4. 稅額 = 加運費後 × 稅率%
            const taxAmount = amountWithShipping * (taxRate / 100);

            // 5. 總金額 = 加運費後 + 稅額
            const totalAmount = amountWithShipping + taxAmount;

            // 更新顯示
            document.getElementById('subtotalDisplay').textContent = 'NT$ ' + subtotal.toLocaleString('zh-TW', {
                minimumFractionDigits: 0
            });
            document.getElementById('subtotalInput').value = subtotal.toFixed(0);

            document.getElementById('taxDisplay').textContent = 'NT$ ' + taxAmount.toLocaleString('zh-TW', {
                minimumFractionDigits: 0
            });
            document.getElementById('taxInput').value = taxAmount.toFixed(0);

            document.getElementById('totalDisplay').textContent = 'NT$ ' + totalAmount.toLocaleString('zh-TW', {
                minimumFractionDigits: 0
            });
            document.getElementById('totalInput').value = totalAmount.toFixed(0);
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