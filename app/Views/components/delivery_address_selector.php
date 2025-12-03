<?php

/**
 * 送貨地址選擇器組件
 * 
 * 使用方式：
 * <?= view('components/delivery_address_selector', [
 *     'fieldName' => 'q_cda_id',  // 表單欄位名稱
 *     'selectedCustomerId' => $data['q_c_id'] ?? '',
 *     'selectedDeliveryAddressId' => $data['q_cda_id'] ?? '',
 *     'deliveryAddressMissing' => false,
 *     'fieldClass' => getFieldClass('q_cda_id'),
 *     'fieldError' => showFieldError('q_cda_id')
 * ]) ?>
 * 
 * @param string $fieldName 表單欄位名稱（如：q_cda_id, o_cda_id）
 * @param string $selectedCustomerId 已選擇的客戶ID
 * @param string $selectedDeliveryAddressId 已選擇的送貨地址ID
 * @param bool $deliveryAddressMissing 送貨地址是否已被刪除
 * @param string $fieldClass 欄位CSS類別（用於錯誤顯示）
 * @param string $fieldError 欄位錯誤訊息HTML
 */

$fieldName = $fieldName ?? 'cda_id';
$selectedCustomerId = $selectedCustomerId ?? '';
$selectedDeliveryAddressId = $selectedDeliveryAddressId ?? '';
$deliveryAddressMissing = $deliveryAddressMissing ?? false;
$fieldClass = $fieldClass ?? '';
$fieldError = $fieldError ?? '';
?>

<div class="row align-items-start mb-3">
    <div class="col-md-6 mb-3">
        <label for="deliveryAddressSelect" class="form-label">
            送貨地址 <span class="text-danger">*</span>
        </label>
        <select
            class="form-select <?= $fieldClass ?>"
            id="deliveryAddressSelect"
            name="<?= esc($fieldName) ?>"
            required
            data-endpoint="<?= base_url('customer/delivery-addresses') ?>"
            data-initial-customer="<?= esc($selectedCustomerId) ?>"
            data-selected-id="<?= esc($selectedDeliveryAddressId) ?>">
            <option value=""></option>
        </select>
        <?= $fieldError ?>
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
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <label class="form-label">縣市</label>
        <input type="text" class="form-control" id="deliveryCity" value="" readonly>
    </div>
    <div class="col-md-9">
        <label class="form-label">送貨地址</label>
        <input type="text" class="form-control" id="deliveryAddressText" value="" readonly>
    </div>
    <div class="col-md-12">
        <label class="form-label">送貨備註</label>
        <textarea class="form-control" id="deliveryAddressNote" rows="2" readonly></textarea>
    </div>
</div>

<script>
    // 送貨地址管理器 - 立即執行函數避免全域污染
    (function() {
        // 等待 DOM 載入完成
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initDeliveryAddressManager);
        } else {
            initDeliveryAddressManager();
        }

        function initDeliveryAddressManager() {
            const select = document.getElementById('deliveryAddressSelect');
            if (!select || select.dataset.initialized === 'true') {
                return; // 已初始化或不存在，跳過
            }

            // 標記為已初始化
            select.dataset.initialized = 'true';

            const manager = {
                select: select,
                customerSelect: document.getElementById('customer'),
                notice: document.getElementById('deliveryAddressNotice'),
                nameInput: document.getElementById('deliveryAddressName'),
                contactInput: document.getElementById('deliveryContact'),
                phoneInput: document.getElementById('deliveryPhone'),
                cityInput: document.getElementById('deliveryCity'),
                addressInput: document.getElementById('deliveryAddressText'),
                noteInput: document.getElementById('deliveryAddressNote'),
                endpoint: select.dataset.endpoint,
                cache: new Map(),
                currentAddresses: [],
                selectedCustomerId: select.dataset.initialCustomer || '',
                selectedAddressId: select.dataset.selectedId || '',
                defaultId: null
            };

            // 綁定客戶選擇事件
            if (manager.customerSelect) {
                manager.customerSelect.addEventListener('change', function() {
                    manager.selectedCustomerId = this.value;
                    manager.selectedAddressId = '';
                    loadAddresses(manager, false);
                });
            }

            // 綁定地址選擇事件
            select.addEventListener('change', function() {
                manager.selectedAddressId = this.value;
                populateDetails(manager);
            });

            // 初始載入
            if (manager.selectedCustomerId) {
                loadAddresses(manager, true);
            } else {
                disableSelect(manager);
                showNotice(manager, '請先選擇客戶以載入送貨地址', 'info');
            }
        }

        function loadAddresses(manager, preserveSelection) {
            if (!manager.endpoint) {
                console.error('送貨地址管理器：未設定 endpoint');
                return;
            }

            const customerId = manager.selectedCustomerId;
            if (!customerId) {
                disableSelect(manager);
                return;
            }

            // 檢查快取
            if (manager.cache.has(customerId)) {
                const cached = manager.cache.get(customerId);
                manager.currentAddresses = cached.data;
                manager.defaultId = cached.defaultId;
                renderOptions(manager, preserveSelection);
                return;
            }

            // 顯示載入中
            showNotice(manager, '正在載入送貨地址...', 'info');

            // 從 API 載入
            fetch(`${manager.endpoint}/${customerId}`)
                .then(response => {
                    if (!response.ok) throw new Error('Network error');
                    return response.json();
                })
                .then(result => {
                    if (!result.success) {
                        throw new Error(result.message || '載入失敗');
                    }

                    const data = result.data || [];
                    const defaultId = result.defaultId || null;

                    // 存入快取
                    manager.cache.set(customerId, {
                        data,
                        defaultId
                    });
                    manager.currentAddresses = data;
                    manager.defaultId = defaultId;

                    renderOptions(manager, preserveSelection);
                })
                .catch(error => {
                    console.error('送貨地址管理器：', error);
                    disableSelect(manager);
                    showNotice(manager, '載入送貨地址失敗，請重新選擇客戶或稍後再試', 'danger');
                });
        }

        function renderOptions(manager, preserveSelection) {
            const addresses = manager.currentAddresses;

            // 清空選項
            manager.select.innerHTML = '<option value=""></option>';

            // 如果沒有地址
            if (!addresses.length) {
                manager.select.setAttribute('disabled', 'disabled');
                manager.selectedAddressId = '';
                populateDetails(manager);
                showNotice(manager, '此客戶尚未設定送貨地址，請至客戶管理新增', 'warning');
                return;
            }

            // 啟用選單
            manager.select.removeAttribute('disabled');

            // 加入選項
            addresses.forEach(address => {
                const option = document.createElement('option');
                option.value = address.cda_id;
                const label = address.cda_name ?
                    `${address.cda_name} - ${address.cda_address}` :
                    address.cda_address;
                option.textContent = label;
                manager.select.appendChild(option);
            });

            // 決定要選擇哪個地址
            let targetId = preserveSelection ? manager.selectedAddressId : null;

            // 如果保留的地址不存在於列表中，清空
            if (targetId && !addresses.some(addr => String(addr.cda_id) === String(targetId))) {
                targetId = null;
            }

            // 使用預設地址
            if (!targetId && manager.defaultId) {
                targetId = manager.defaultId;
            }

            // 使用第一個地址
            if (!targetId && addresses.length > 0) {
                targetId = addresses[0].cda_id;
            }

            // 設定選中的地址
            manager.selectedAddressId = targetId ? String(targetId) : '';
            manager.select.value = manager.selectedAddressId;

            hideNotice(manager);
            populateDetails(manager);
        }

        function populateDetails(manager) {
            const selected = manager.currentAddresses.find(
                addr => String(addr.cda_id) === String(manager.selectedAddressId)
            );

            if (manager.nameInput) {
                manager.nameInput.value = selected ? (selected.cda_name || '') : '';
            }
            if (manager.contactInput) {
                manager.contactInput.value = selected ? (selected.cda_contact_person || '') : '';
            }
            if (manager.phoneInput) {
                manager.phoneInput.value = selected ? (selected.cda_phone || '') : '';
            }
            if (manager.cityInput) {
                manager.cityInput.value = selected ? (selected.cda_city || '') : '';
            }
            if (manager.addressInput) {
                manager.addressInput.value = selected ? (selected.cda_address || '') : '';
            }
            if (manager.noteInput) {
                manager.noteInput.value = selected ? (selected.cda_notes || '') : '';
            }
        }

        function showNotice(manager, message, type = 'warning') {
            if (!manager.notice) return;

            manager.notice.textContent = message;
            manager.notice.classList.remove('d-none', 'alert-warning', 'alert-info', 'alert-danger', 'alert-success');
            manager.notice.classList.add(`alert-${type}`);
        }

        function hideNotice(manager) {
            if (!manager.notice) return;
            manager.notice.classList.add('d-none');
        }

        function disableSelect(manager) {
            manager.select.setAttribute('disabled', 'disabled');
            manager.select.innerHTML = '<option value="">請先選擇客戶</option>';
            manager.selectedAddressId = '';
            populateDetails(manager);
        }
    })();
</script>