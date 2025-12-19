<?php

/**
 * 報價單商品項目行組件
 * 
 * 使用方式：
 * 1. 在 PHP 循環中渲染現有項目：
 *    foreach ($items as $index => $item) {
 *        echo view('components/quote_item_row', [
 *            'index' => $index,
 *            'item' => $item,
 *            'products' => $products,
 *            'isTemplate' => false
 *        ]);
 *    }
 * 
 * 2. 作為 JavaScript 模板：
 *    <template id="itemRowTemplate">
 *        <?= view('components/quote_item_row', ['index' => '__INDEX__', 'item' => [], 'products' => $products, 'isTemplate' => true]) ?>
 *    </template>
 * 
 * @param int|string $index 項目索引
 * @param array $item 項目資料
 * @param array $products 商品列表
 * @param bool $isTemplate 是否為 JavaScript 模板
 */

$index = $index ?? 0;
$item = $item ?? [];
$products = $products ?? [];
$productCategories = $productCategories ?? [];
$isTemplate = $isTemplate ?? false;

// 預設值
$defaults = [
    'qi_id' => '',
    'qi_p_id' => '',
    'qi_quantity' => 1,
    'qi_unit_price' => 0,
    'qi_discount' => 0,
];

$item = array_merge($defaults, $item);

// 找出選中商品的分類
$selectedCategoryId = '';
if (!empty($item['qi_p_id'])) {
    foreach ($products as $p) {
        if ($p['p_id'] == $item['qi_p_id']) {
            $selectedCategoryId = $p['p_pc_id'] ?? '';
            break;
        }
    }
}
?>

<?php
// 取得產品圖片（若已有選定商品）
$selectedImage = '';
if (!empty($item['qi_p_id'])) {
    foreach ($products as $p) {
        if ($p['p_id'] == $item['qi_p_id']) {
            $rawImage = $p['p_image'] ?? '';
            if ($rawImage) {
                // p_image 已包含完整相對路徑（如：uploads/products/xxx.jpg）
                $selectedImage = base_url($rawImage);
            }
            break;
        }
    }
}
$placeholder = base_url('images/placeholder.png');
?>
<tr class="item-row"
    data-selected-supplier="<?= esc($item['qi_supplier'] ?? '') ?>"
    data-selected-color="<?= esc($item['qi_color'] ?? '') ?>"
    data-selected-size="<?= esc($item['qi_size'] ?? '') ?>">
    <!-- 商品圖片 -->
    <td style="width: 10%;" class="align-middle">
        <input type="hidden" name="items[<?= $index ?>][qi_id]" value="<?= esc($item['qi_id']) ?>">
        <div class="ratio ratio-1x1 border rounded overflow-hidden bg-light shadow-sm">
            <img src="<?= esc($selectedImage ?: $placeholder) ?>"
                class="img-fluid item-image-preview object-fit-cover"
                alt="商品圖片"
                data-placeholder="<?= esc($placeholder) ?>"
                style="cursor: pointer;"
                title="點擊查看大圖">
        </div>
    </td>
    <!-- 商品資訊 -->
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
                name="items[<?= $index ?>][qi_p_id]"
                data-index="<?= $index ?>"
                title="選擇商品"
                required>
                <option value="">請選擇商品</option>
                <?php foreach ($products as $product): ?>
                    <option value="<?= $product['p_id'] ?>"
                        data-price="<?= $product['p_standard_price'] ?>"
                        data-category="<?= $product['p_pc_id'] ?? '' ?>"
                        data-supplier="<?= esc($product['p_supplier'] ?? '') ?>"
                        data-color="<?= esc($product['p_color'] ?? '') ?>"
                        data-size="<?= esc($product['p_size'] ?? '') ?>"
                        data-image="<?= esc($product['p_image'] ?? '') ?>"
                        <?= ($item['qi_p_id'] == $product['p_id']) ? 'selected' : '' ?>>
                        <?= esc($product['p_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </td>
    <td style="width: 10%;" class="align-middle">
        <select class="form-select form-select-sm supplier-select small"
            name="items[<?= $index ?>][qi_supplier]"
            title="供應商">
            <option value="">-</option>
        </select>
    </td>
    <td style="width: 10%;" class="align-middle">
        <select class="form-select form-select-sm color-select small"
            name="items[<?= $index ?>][qi_color]"
            title="顏色">
            <option value="">-</option>
        </select>
    </td>
    <td style="width: 10%;" class="align-middle">
        <select class="form-select form-select-sm size-select small"
            name="items[<?= $index ?>][qi_size]"
            title="尺寸">
            <option value="">-</option>
        </select>
    </td>
    <td style="width: 7%;" class="align-middle">
        <input type="number" class="form-control form-control-sm quantity-input text-center"
            name="items[<?= $index ?>][qi_quantity]"
            value="<?= esc($item['qi_quantity']) ?>"
            min="1" step="1"
            title="數量"
            required>
    </td>
    <td style="width: 10%;" class="align-middle">
        <input type="number" class="form-control form-control-sm price-input text-end"
            name="items[<?= $index ?>][qi_unit_price]"
            value="<?= esc($item['qi_unit_price']) ?>"
            min="0" step="1"
            title="單價"
            required>
    </td>
    <td style="width: 7%;" class="align-middle">
        <input type="number" class="form-control form-control-sm discount-input text-center"
            name="items[<?= $index ?>][qi_discount]"
            value="<?= esc($item['qi_discount']) ?>"
            min="0" max="100" step="0.01"
            title="折扣百分比">
    </td>
    <td style="width: 10%;" class="align-middle">
        <input type="text" class="form-control form-control-sm amount-display text-end bg-light fw-bold"
            value="0"
            title="小計金額"
            readonly>
    </td>
    <td class="text-center align-middle" style="width: 4%;">
        <button type="button" class="btn btn-sm btn-outline-danger remove-item" title="刪除此項目">
            <i class="bi bi-trash"></i>
        </button>
    </td>
</tr>