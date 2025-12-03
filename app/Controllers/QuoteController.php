<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\QuoteModel;
use App\Models\QuoteItemModel;
use App\Models\CustomerModel;
use App\Models\ProductModel;
use App\Models\ProductCategoryModel;
use App\Models\CustomerDeliveryAddressModel;

class QuoteController extends BaseController
{
    private $quoteModel;
    private $quoteItemModel;
    private $customerModel;
    private $productModel;
    private $productCategoryModel;
    private $deliveryAddressModel;

    public function __construct()
    {
        $this->quoteModel = new QuoteModel();
        $this->quoteItemModel = new QuoteItemModel();
        $this->customerModel = new CustomerModel();
        $this->productModel = new ProductModel();
        $this->productCategoryModel = new ProductCategoryModel();
        $this->deliveryAddressModel = new CustomerDeliveryAddressModel();
    }

    /**
     * 顯示報價單列表
     */
    public function index()
    {
        $keyword = $this->request->getGet('keyword');
        $page = $this->request->getGet('page') ?: 1;
        $perPage = 10;

        // 使用 Model 的分頁查詢方法
        $result = $this->quoteModel->getQuotesWithPagination($keyword, $page, $perPage);

        return view('quote/index', [
            'data' => $result['data'],
            'keyword' => $keyword,
            'pager' => [
                'currentPage' => $page,
                'totalPages' => $result['totalPages']
            ]
        ]);
    }

    /**
     * 顯示新增報價單表單
     */
    public function create()
    {
        $customers = $this->customerModel->findAll();
        $products = $this->productModel->findAll();
        $productCategories = $this->productCategoryModel->getAllForDropdown();
        $quoteNumber = $this->quoteModel->generateQuoteNumber();

        return view('quote/form', [
            'isEdit' => false,
            'customers' => $customers,
            'products' => $products,
            'productCategories' => $productCategories,
            'deliveryAddressMissing' => false,
            'quoteNumber' => $quoteNumber,
        ]);
    }

    /**
     * 顯示編輯報價單表單
     */
    public function edit($id)
    {
        $quote = $this->quoteModel->find($id);

        if (!$quote) {
            return redirect()->to(url_to('QuoteController::index'))
                ->with('error', '報價單不存在');
        }

        // 取得報價單項目
        $items = $this->quoteItemModel->getItemsWithProduct($id);
        $quote['items'] = $items;

        $customers = $this->customerModel->findAll();
        $products = $this->productModel->findAll();
        $productCategories = $this->productCategoryModel->getAllForDropdown();

        $deliveryAddressMissing = false;
        if (!empty($quote['q_cda_id']) && !$this->deliveryAddressModel->find($quote['q_cda_id'])) {
            $deliveryAddressMissing = true;
            $quote['q_cda_id'] = null;
        }

        return view('quote/form', [
            'isEdit' => true,
            'data' => $quote,
            'customers' => $customers,
            'products' => $products,
            'productCategories' => $productCategories,
            'deliveryAddressMissing' => $deliveryAddressMissing,
        ]);
    }

    /**
     * 儲存報價單（新增/編輯）
     */
    public function save()
    {
        $quoteData = $this->request->getPost();
        $items = $this->request->getPost('items') ?: [];

        // 檢查報價單號唯一性
        $quoteId = $quoteData['q_id'] ?? null;
        $quoteNumber = $quoteData['q_number'] ?? '';

        if (!$this->quoteModel->isQuoteNumberUnique($quoteNumber, $quoteId)) {
            return redirect()->back()
                ->withInput()
                ->with('error', '報價單號已存在，請使用其他編號');
        }

        // 使用 Model 驗證項目
        $validation = $this->quoteModel->validateItems($items);
        if (!$validation['valid']) {
            return redirect()->back()
                ->withInput()
                ->with('error', $validation['message']);
        }

        // 驗證送貨地址
        $customerId = $quoteData['q_c_id'] ?? null;
        $deliveryAddressId = $quoteData['q_cda_id'] ?? null;

        if (empty($customerId) || empty($deliveryAddressId)) {
            return redirect()->back()
                ->withInput()
                ->with('error', '請選擇送貨地址');
        }

        $address = $this->deliveryAddressModel->find($deliveryAddressId);
        if (!$address || intval($address['cda_c_id']) !== intval($customerId)) {
            return redirect()->back()
                ->withInput()
                ->with('error', '送貨地址無效，請重新選擇');
        }

        // 使用 Model 儲存（含事務處理）
        $result = $this->quoteModel->saveQuoteWithItems($quoteData, $items);

        if (!$result['success']) {
            return redirect()->back()
                ->withInput()
                ->with('error', $result['message']);
        }

        return redirect()->to(url_to('QuoteController::index'));
    }

    /**
     * 刪除報價單
     */
    public function delete($id)
    {
        $quote = $this->quoteModel->find($id);

        if (!$quote) {
            return redirect()->to(url_to('QuoteController::index'))
                ->with('error', '報價單不存在');
        }

        // 因為設定了 CASCADE，刪除報價單時會自動刪除相關的項目
        $this->quoteModel->delete($id);

        return redirect()->to(url_to('QuoteController::index'));
    }

    /**
     * AJAX: 取得商品資料
     */
    public function getProduct($id)
    {
        if ($this->request->isAJAX()) {
            $product = $this->productModel->find($id);
            return $this->response->setJSON($product);
        }

        return $this->response->setStatusCode(400);
    }
}
