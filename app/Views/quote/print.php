<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>報價單 - <?= esc($data['q_number']) ?></title>
    <?php
    // 檢查是否有任何商品有折扣
    $hasDiscount = false;
    if (!empty($data['items'])) {
        foreach ($data['items'] as $item) {
            if (!empty($item['qi_discount']) && $item['qi_discount'] > 0) {
                $hasDiscount = true;
                break;
            }
        }
    }
    
    // 定義 SVG placeholder（避免 404 錯誤）
    $placeholderImage = 'data:image/svg+xml;base64,' . base64_encode('
        <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100">
            <rect width="100" height="100" fill="#f0f0f0"/>
            <text x="50" y="50" font-family="Arial" font-size="12" fill="#999" text-anchor="middle" dominant-baseline="middle">無圖片</text>
        </svg>
    ');
    ?>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Microsoft JhengHei", "PingFang TC", "Helvetica Neue", Arial, sans-serif;
            font-size: 12pt;
            line-height: 1.6;
            color: #333;
            background-color: #f0f0f0;
        }

        /* A4 容器 */
        .a4-container {
            width: 210mm;
            min-height: 297mm;
            background-color: white;
            padding: 10mm;
            margin: 0 auto 20px auto;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        /* A4 尺寸 */
        @page {
            size: A4;
            margin: 10mm;
        }

        /* 頁首 */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .header-left img {
            width: 250px;
            height: 100px;
            object-fit: contain;
        }

        .header-left-text h2 {
            font-size: 18pt;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .header-left-text p {
            font-size: 10pt;
            color: #666;
        }

        .header-right {
            text-align: right;
            font-size: 10pt;
            line-height: 1.8;
        }

        .header-right h3 {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 5px;
        }

        /* 報價單標題 */
        .title-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #333;
            margin-bottom: 5px;
        }

        .title {
            flex: 1;
            text-align: center;
            font-size: 20pt;
            font-weight: bold;
        }

        .page-number {
            font-size: 10pt;
            color: #666;
            white-space: nowrap;
            padding-right: 10px;
        }

        /* 客戶與單據資訊 */
        .info-section {
            margin-bottom: 10px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11pt;
        }

        .info-table td {
            padding: 5px 8px;
            vertical-align: middle;
        }

        .info-table .label {
            color: #333;
            font-weight: bold;
            text-align: justify;
            text-align-last: justify;
        }

        .info-table .value {
            font-weight: bold;
            color: #000;
            word-break: break-all;
        }

        /* 商品明細表格 */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 11pt;
        }

        .items-table th {
            background-color: #f5f5f5;
            border: 1px solid #333;
            padding: 8px;
            text-align: center;
            font-weight: bold;
        }

        .items-table td {
            border: 1px solid #333;
            padding: 8px;
            vertical-align: middle;
        }

        .items-table .img-cell {
            text-align: center;
            width: 110px;
        }

        .items-table .img-cell img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border: 1px solid #ddd;
        }

        .items-table .desc-cell {
            padding-left: 10px;
        }

        .items-table .number-cell {
            text-align: center;
            width: 80px;
        }

        .items-table .price-cell {
            text-align: right;
            width: 100px;
        }

        .items-table .amount-cell {
            text-align: right;
            width: 120px;
            font-weight: bold;
        }

        .items-table .discount-cell {
            text-align: center;
            width: 80px;
            color: #d9534f;
        }

        .product-name {
            font-weight: bold;
        }

        .product-spec {
            font-size: 10pt;
            color: #666;
        }

        /* 金額計算區 */
        .summary {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 15px;
        }

        .summary-table {
            width: 350px;
            border-collapse: collapse;
        }

        .summary-table td {
            padding: 5px 10px;
            font-size: 11pt;
        }

        .summary-table .label {
            text-align: right;
            color: #666;
        }

        .summary-table .value {
            text-align: right;
            font-weight: bold;
            width: 120px;
        }

        .summary-table .discount-row .label,
        .summary-table .discount-row .value {
            color: #d9534f;
        }

        .summary-table .total-row {
            border-top: 2px solid #333;
            font-size: 13pt;
        }

        .summary-table .total-row .label {
            color: #333;
            font-weight: bold;
        }

        /* 匯款資訊 */
        .bank-info {
            display: flex;
            gap: 30px;
            margin-bottom: 15px;
            font-size: 10pt;
            padding: 10px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
        }

        .bank-info div {
            flex: 1;
        }

        /* 注意事項 */
        .notes {
            margin-bottom: 15px;
            font-size: 10pt;
        }

        .notes h4 {
            font-size: 11pt;
            margin-bottom: 5px;
        }

        .notes ol {
            padding-left: 20px;
            line-height: 1.8;
        }

        /* 簽名區 */
        .signature {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }

        .signature-box {
            width: 45%;
            padding: 10px;
            min-height: 80px;
            border-radius: 4px;
        }

        .signature-box label {
            font-weight: bold;
            display: block;
            margin-bottom: 10px;
            color: #666;
        }

        /* 最後一頁的內容 */
        .last-page-content {
            margin-top: 20px;
        }

        /* 列印樣式 */
        @media print {
            body {
                background-color: white;
                padding: 0;
                margin: 0;
            }

            .a4-container {
                width: 210mm;
                min-height: auto;
                box-shadow: none;
                margin: 0;
                padding: 10mm;
                page-break-after: always;
                break-after: page;
            }

            .a4-container.last-page {
                page-break-after: avoid;
                break-after: avoid;
            }

            .no-print {
                display: none !important;
            }

            @page {
                size: A4 portrait;
                margin: 10mm;
            }
        }

        /* 列印按鈕 */
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14pt;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .print-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <!-- 列印按鈕 -->
    <button class="print-button no-print" onclick="window.print()">🖨️ 列印</button>

    <?php
    // 手動分頁處理：每頁顯示5個商品
    $itemsPerPage = 5;
    $items = $data['items'] ?? [];
    $totalItems = count($items);

    // 如果沒有商品，至少顯示一頁
    if ($totalItems === 0) {
        $totalPages = 1;
        $itemGroups = [[]]; // 空陣列作為第一頁
    } else {
        // 將 ceil() 的結果轉換為 integer，避免類型不匹配
        $totalPages = (int)ceil($totalItems / $itemsPerPage);
        $itemGroups = array_chunk($items, $itemsPerPage);
    }
    ?>

    <?php for ($pageNum = 0; $pageNum < $totalPages; $pageNum++): ?>
        <?php
        $isFirstPage = ($pageNum === 0);
        $isLastPage = ($pageNum === $totalPages - 1);
        $currentItems = isset($itemGroups[$pageNum]) ? $itemGroups[$pageNum] : [];
        ?>

        <!-- A4 容器 -->
        <div class="a4-container <?= $isLastPage ? 'last-page' : '' ?>">
            <!-- 頁首（每頁都顯示） -->
            <div class="header">
                <div class="header-left">
                    <img src="<?= base_url('img/LOGO.png') ?>" alt="嵐石事業有限公司">
                </div>
                <div class="header-right">
                    <h3>嵐石事業有限公司</h3>
                    <div>330桃園市桃園區藝文一街86之5號4樓</div>
                    <div>TEL：03-2605957　統編：24615098</div>
                </div>
            </div>

            <!-- 報價單標題（每頁都顯示） -->
            <div class="title-row">
                <div class="title">報 價 單</div>
                <?php if ($totalPages > 1): ?>
                    <div class="page-number">第 <?= $pageNum + 1 ?> 頁 / 共 <?= $totalPages ?> 頁</div>
                <?php endif; ?>
            </div>

            <!-- 客戶與單據資訊（只在第一頁顯示） -->
            <?php if ($isFirstPage): ?>
                <div class="info-section first-page-only">
                    <table class="info-table">
                        <colgroup>
                            <col style="width: 14%;">
                            <col style="width: 20%;">
                            <col style="width: 14%;">
                            <col style="width: 20%;">
                            <col style="width: 14%;">
                            <col style="width: 18%;">
                        </colgroup>
                        <tr>
                            <td class="label">客戶名稱：</td>
                            <td class="value" colspan="3"><?= esc($data['customer']['c_name'] ?? '') ?></td>
                            <td class="label">報價日期：</td>
                            <td class="value"><?= esc($data['q_date']) ?></td>
                        </tr>
                        <tr>
                            <td class="label">聯絡人：</td>
                            <td class="value"><?= esc($data['contact']['cc_name'] ?? '') ?></td>
                            <td class="label">統編：</td>
                            <td class="value"><?= esc($data['customer']['c_tax_id'] ?? '') ?></td>
                            <td class="label">有效期限：</td>
                            <td class="value">
                                <?php
                                if (!empty($data['q_valid_date'])) {
                                    $validDate = new DateTime($data['q_valid_date']);
                                    $quoteDate = new DateTime($data['q_date']);
                                    $diff = $quoteDate->diff($validDate);
                                    echo $diff->days . '天';
                                } else {
                                    echo '-';
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="label">市話：</td>
                            <td class="value"><?= esc($data['customer']['c_phone'] ?? '') ?></td>
                            <td class="label">手機：</td>
                            <td class="value"><?= esc($data['contact']['cc_phone'] ?? '') ?></td>
                            <td class="label">經辦人員：</td>
                            <td class="value"><?= esc($data['q_vendor'] ?? '') ?></td>
                        </tr>
                        <tr>
                            <td class="label">傳真：</td>
                            <td class="value"><?= esc($data['customer']['c_fax'] ?? '') ?></td>
                            <td class="label">Email：</td>
                            <td class="value" colspan="3"><?= esc($data['contact']['cc_email'] ?? '') ?></td>
                        </tr>
                        <tr>
                            <td class="label">客戶地址：</td>
                            <td class="value" colspan="5"><?= esc($data['customer']['c_city'] ?? '') ?> <?= esc($data['customer']['c_address'] ?? '') ?></td>
                        </tr>
                        <tr>
                            <td class="label">送貨地址：</td>
                            <td class="value" colspan="5"><?= esc($data['q_delivery_city'] ?? '') ?> <?= esc($data['q_delivery_address'] ?? '') ?></td>
                        </tr>
                    </table>
                </div>
            <?php endif; ?>

            <!-- 商品明細表格 -->
            <table class="items-table">
                <thead>
                    <tr>
                        <th>品名/規格 (DESCRIPTION)</th>
                        <th>參考圖</th>
                        <th>數量</th>
                        <th>單價</th>
                        <?php if ($hasDiscount): ?>
                            <th>折扣</th>
                        <?php endif; ?>
                        <th>金額</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($currentItems)): ?>
                        <?php foreach ($currentItems as $item): ?>
                            <?php
                            // 處理圖片路徑（使用 product_images）
                            $imagePath = $placeholderImage;
                            if (!empty($item['pi_name']) && !empty($item['pi_p_id'])) {
                                $imagePath = base_url('uploads/products/' . $item['pi_p_id'] . '/' . $item['pi_name']);
                            }

                            // 圖片檔名就是顏色/花色
                            $colorSpec = !empty($item['pi_name']) ? pathinfo($item['pi_name'], PATHINFO_FILENAME) : '';
                            ?>
                            <tr>
                                <td class="desc-cell">
                                    <div class="product-name"><?= esc($item['p_name']) ?></div>
                                    <?php if ($colorSpec): ?>
                                        <div class="product-spec">顏色/花色: <?= esc($colorSpec) ?></div>
                                    <?php endif; ?>
                                    <?php if ($item['qi_size']): ?>
                                        <div class="product-spec">尺寸: <?= esc($item['qi_size']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="img-cell">
                                    <img src="<?= esc($imagePath) ?>" alt="" onerror="this.src='<?= $placeholderImage ?>'">
                                </td>
                                <td class="number-cell"><?= $item['qi_quantity'] ?></td>
                                <td class="price-cell"><?= number_format($item['qi_unit_price'], 0) ?></td>
                                <?php if ($hasDiscount): ?>
                                    <td class="discount-cell">
                                        <?php if (!empty($item['qi_discount']) && $item['qi_discount'] > 0): ?>
                                            -<?= floatval($item['qi_discount']) ?>%
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>
                                <td class="amount-cell"><?= number_format($item['qi_amount'], 0) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <!-- 運費（只在最後一頁顯示） -->
                    <?php if ($isLastPage && !empty($data['q_shipping_fee']) && $data['q_shipping_fee'] > 0): ?>
                        <tr>
                            <td class="desc-cell">運費</td>
                            <td class="img-cell"></td>
                            <td class="number-cell">1</td>
                            <td class="price-cell"><?= number_format($data['q_shipping_fee'], 0) ?></td>
                            <?php if ($hasDiscount): ?>
                                <td class="discount-cell">-</td>
                            <?php endif; ?>
                            <td class="amount-cell"><?= number_format($data['q_shipping_fee'], 0) ?></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- 最後一頁內容（只在最後一頁顯示） -->
            <?php if ($isLastPage): ?>
                <div class="last-page-content">
                    <!-- 金額計算 -->
                    <div class="summary">
                        <table class="summary-table">
                            <tr>
                                <td class="label">小計 (Subtotal)</td>
                                <td class="value"><?= number_format($data['q_subtotal'], 0) ?></td>
                            </tr>
                            <?php if ($data['q_discount'] > 0): ?>
                                <tr class="discount-row">
                                    <td class="label">折扣後金額 <?= floatval($data['q_discount']) ?> %OFF</td>
                                    <td class="value"><?= number_format($data['q_subtotal'] * (1 - $data['q_discount'] / 100), 0) ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if (!empty($data['q_shipping_fee']) && $data['q_shipping_fee'] > 0): ?>
                                <tr>
                                    <td class="label">運費</td>
                                    <td class="value"><?= number_format($data['q_shipping_fee'], 0) ?></td>
                                </tr>
                            <?php endif; ?>
                            <tr>
                                <td class="label">稅金 (Tax <?= floatval($data['q_tax_rate']) ?>%)</td>
                                <td class="value"><?= number_format($data['q_tax_amount'], 0) ?></td>
                            </tr>
                            <tr class="total-row">
                                <td class="label">總計 (Total)</td>
                                <td class="value"><?= number_format($data['q_total_amount'], 0) ?></td>
                            </tr>
                        </table>
                    </div>

                    <!-- 匯款資訊 -->
                    <div class="bank-info">
                        <div>
                            <strong>匯款資訊</strong><br>
                            銀行：華南銀行(008) 北桃園分行<br>
                            帳號：244-10-002919-7
                        </div>
                        <div>
                            <br>
                            戶名：嵐石事業有限公司<br>
                            付款條件：訂金 50%，餘款 50%
                        </div>
                    </div>

                    <!-- 注意事項 -->
                    <div class="notes">
                        <h4>注意事項：</h4>
                        <ol>
                            <li>本報價僅為材料費，不包含運費、搬運、施工安裝及垃圾清運等任何工程費用。</li>
                            <li>不同生產批號之產品可能存在微小色差，建築於同一空間使用同一批號之材料，訂購時應按實際尺寸一次訂足。</li>
                            <li>交期將於訂金確認收取後另行通知。</li>
                        </ol>
                    </div>

                    <!-- 簽名區 -->
                    <div class="signature">
                        <div class="signature-box">
                            <label>經辦：</label>
                        </div>
                        <div class="signature-box">
                            <label>客戶簽章：</label>
                        </div>
                    </div>
                </div>
                <!-- 最後一頁內容結束 -->
            <?php endif; ?>
        </div>
        <!-- A4 容器結束 -->

    <?php endfor; ?>
</body>

</html>