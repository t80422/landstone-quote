<?php

/**
 * 訂單商品項目行組件
 * 
 * 使用方式：
 * 1. 在 PHP 循環中渲染現有項目：
 *    foreach ($items as $index => $item) {
 *        echo view('components/order_item_row', [
 *            'index' => $index,
 *            'item' => $item,
 *            'products' => $products,
 *            'productCategories' => $productCategories,
 *            'isTemplate' => false
 *        ]);
 *    }
 * 
 * 2. 作為 JavaScript 模板：
 *    <template id="itemRowTemplate">
 *        <?= view('components/order_item_row', ['index' => '__INDEX__', 'item' => [], 'products' => $products, 'productCategories' => $productCategories, 'isTemplate' => true]) ?>
 *    </template>
 * 
 * @param int|string $index 項目索引
 * @param array $item 項目資料
 * @param array $products 商品列表
 * @param array $productCategories 商品分類列表
 * @param bool $isTemplate 是否為 JavaScript 模板
 */

$index = $index ?? 0;
$item = $item ?? [];
$products = $products ?? [];
$productCategories = $productCategories ?? [];
$isTemplate = $isTemplate ?? false;

// 預設值
$defaults = [
    'oi_id' => '',
    'oi_p_id' => '',
    'oi_quantity' => 1,
    'oi_unit_price' => 0,
    'oi_discount' => 0,
    'oi_shipped_quantity' => 0,
    'oi_supplier' => '',
    'oi_style' => '',
    'oi_color' => '',
    'oi_size' => '',
];

$item = array_merge($defaults, $item);

// 已出貨數量
$shippedQty = $item['oi_shipped_quantity'] ?? 0;
$hasShipped = $shippedQty > 0;

// 找出選中商品的分類
$selectedCategoryId = '';
if (!empty($item['oi_p_id'])) {
    foreach ($products as $p) {
        if ($p['p_id'] == $item['oi_p_id']) {
            $selectedCategoryId = $p['p_pc_id'] ?? '';
            break;
        }
    }
}

// 取得產品圖片（若已有選定商品）
$selectedImage = '';
if (!empty($item['oi_p_id'])) {
    foreach ($products as $p) {
        if ($p['p_id'] == $item['oi_p_id']) {
            $rawImage = $p['p_image'] ?? '';
            if ($rawImage) {
                $selectedImage = base_url($rawImage);
            }
            break;
        }
    }
}
$placeholder = base_url('images/placeholder.png');
?>
<tr class="item-row"
    data-shipped-qty="<?= $shippedQty ?>"
    data-selected-supplier="<?= esc($item['oi_supplier'] ?? '') ?>"
    data-selected-style="<?= esc($item['oi_style'] ?? '') ?>"
    data-selected-color="<?= esc($item['oi_color'] ?? '') ?>"
    data-selected-size="<?= esc($item['oi_size'] ?? '') ?>">
    <td style="width: 10%;" class="align-middle">
        <input type="hidden" name="items[<?= $index ?>][oi_id]" value="<?= esc($item['oi_id']) ?>">
        <div class="ratio ratio-1x1 border rounded overflow-hidden bg-light shadow-sm">
            <img src="<?= esc($selectedImage ?: $placeholder) ?>"
                class="img-fluid item-image-preview object-fit-cover"
                alt="商品圖片"
                data-placeholder="<?= esc($placeholder) ?>"
                style="cursor: pointer;"
                title="點擊查看大圖">
        </div>
    </td>
    <td style="width: 20%;" class="align-middle">
        <div class="d-flex flex-column gap-2">
            <select class="form-select form-select-sm category-select" data-index="<?= $index ?>" title="商品分類">
                <option value="">全部分類</option>
                <?php foreach ($productCategories as $category): ?>
                    <option value="<?= $category['pc_id'] ?>" <?= ($selectedCategoryId == $category['pc_id']) ? 'selected' : '' ?>>
                        <?= esc($category['pc_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <select class="form-select form-select-sm product-select"
                name="items[<?= $index ?>][oi_p_id]"
                data-index="<?= $index ?>"
                title="選擇商品"
                required>
                <option value="">請選擇商品</option>
                <?php foreach ($products as $product): ?>
                    <option value="<?= $product['p_id'] ?>"
                        data-price="<?= $product['p_standard_price'] ?>"
                        data-category="<?= $product['p_pc_id'] ?? '' ?>"
                        data-supplier="<?= esc($product['p_supplier'] ?? '') ?>"
                        data-style="<?= esc($product['p_style'] ?? '') ?>"
                        data-color="<?= esc($product['p_color'] ?? '') ?>"
                        data-size="<?= esc($product['p_size'] ?? '') ?>"
                        data-image="<?= esc($product['p_image'] ?? '') ?>"
                        <?= ($item['oi_p_id'] == $product['p_id']) ? 'selected' : '' ?>>
                        <?= esc($product['p_code']) ?> - <?= esc($product['p_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php if ($hasShipped): ?>
            <small class="text-muted">已出貨：<?= $shippedQty ?></small>
        <?php endif; ?>
    </td>
    <td style="width: 8%;" class="align-middle">
        <select class="form-select form-select-sm supplier-select small"
            name="items[<?= $index ?>][oi_supplier]"
            title="供應商">
            <option value="">-</option>
        </select>
    </td>
    <td style="width: 8%;" class="align-middle">
        <select class="form-select form-select-sm style-select small"
            name="items[<?= $index ?>][oi_style]"
            title="款式">
            <option value="">-</option>
        </select>
    </td>
    <td style="width: 8%;" class="align-middle">
        <select class="form-select form-select-sm color-select small"
            name="items[<?= $index ?>][oi_color]"
            title="顏色">
            <option value="">-</option>
        </select>
    </td>
    <td style="width: 8%;" class="align-middle">
        <select class="form-select form-select-sm size-select small"
            name="items[<?= $index ?>][oi_size]"
            title="尺寸">
            <option value="">-</option>
        </select>
    </td>
    <td style="width: 7%;" class="align-middle">
        <input type="number" class="form-control form-control-sm quantity-input text-center"
            name="items[<?= $index ?>][oi_quantity]"
            value="<?= esc($item['oi_quantity']) ?>"
            min="<?= $shippedQty > 0 ? $shippedQty : 1 ?>" step="1"
            data-original-qty="<?= $item['oi_quantity'] ?? 1 ?>"
            title="數量"
            required>
    </td>
    <td style="width: 10%;" class="align-middle">
        <input type="number" class="form-control form-control-sm price-input text-end"
            name="items[<?= $index ?>][oi_unit_price]"
            value="<?= esc($item['oi_unit_price']) ?>"
            min="0" step="1"
            title="單價"
            required>
    </td>
    <td style="width: 7%;" class="align-middle">
        <input type="number" class="form-control form-control-sm discount-input text-center"
            name="items[<?= $index ?>][oi_discount]"
            value="<?= esc($item['oi_discount']) ?>"
            min="0" max="100" step="0.01"
            title="折扣百分比">
    </td>
    <td style="width: 10%;" class="align-middle">
        <input type="text" class="form-control form-control-sm amount-display text-end bg-light fw-bold"
            value="<?= $item['oi_amount'] ?? 0 ?>"
            title="小計金額"
            readonly>
    </td>
    <td class="text-center align-middle" style="width: 4%;">
        <button type="button" class="btn btn-sm btn-outline-danger remove-item"
            title="刪除此項目"
            <?= $hasShipped ? 'disabled title="此商品已有出貨記錄，無法刪除"' : '' ?>>
            <i class="bi bi-trash"></i>
        </button>
    </td>
</tr>