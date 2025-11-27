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

// 找出選中商品的單位（用於初始顯示）
$selectedUnit = '';
if (!empty($item['qi_p_id'])) {
    foreach ($products as $p) {
        if ($p['p_id'] == $item['qi_p_id']) {
            $selectedUnit = $p['p_unit'] ?? '';
            break;
        }
    }
}
?>

<tr class="item-row">
    <td>
        <input type="hidden" name="items[<?= $index ?>][qi_id]" value="<?= esc($item['qi_id']) ?>">
        <select class="form-select form-select-sm product-select"
            name="items[<?= $index ?>][qi_p_id]"
            data-index="<?= $index ?>"
            required>
            <option value=""></option>
            <?php foreach ($products as $product): ?>
                <option value="<?= $product['p_id'] ?>"
                    data-price="<?= $product['p_standard_price'] ?>"
                    data-unit="<?= $product['p_unit'] ?? '' ?>"
                    <?= ($item['qi_p_id'] == $product['p_id']) ? 'selected' : '' ?>>
                    <?= esc($product['p_code']) ?> - <?= esc($product['p_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </td>
    <td>
        <input type="number" class="form-control form-control-sm quantity-input"
            name="items[<?= $index ?>][qi_quantity]"
            value="<?= esc($item['qi_quantity']) ?>"
            min="1" step="1" required>
    </td>
    <td>
        <input type="number" class="form-control form-control-sm price-input"
            name="items[<?= $index ?>][qi_unit_price]"
            value="<?= esc($item['qi_unit_price']) ?>"
            min="0" step="1" required>
    </td>
    <td>
        <input type="text" class="form-control form-control-sm unit-display"
            value="<?= esc($selectedUnit) ?>" readonly>
    </td>
    <td>
        <input type="number" class="form-control form-control-sm discount-input"
            name="items[<?= $index ?>][qi_discount]"
            value="<?= esc($item['qi_discount']) ?>"
            min="0" max="100" step="0.01">
    </td>
    <td>
        <input type="text" class="form-control form-control-sm amount-display"
            value="0" readonly>
    </td>
    <td class="text-center">
        <button type="button" class="btn btn-sm btn-outline-danger remove-item">
            <i class="bi bi-trash"></i>
        </button>
    </td>
</tr>