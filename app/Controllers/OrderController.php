<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\OrderModel;
use App\Models\CustomerModel;
use App\Models\ProductModel;
use App\Models\ProductCategoryModel;
use App\Models\CustomerDeliveryAddressModel;

class OrderController extends BaseController
{
    private $orderModel;
    private $customerModel;
    private $productModel;
    private $productCategoryModel;
    private $deliveryAddressModel;

    public function __construct()
    {
        $this->orderModel = new OrderModel();
        $this->customerModel = new CustomerModel();
        $this->productModel = new ProductModel();
        $this->productCategoryModel = new ProductCategoryModel();
        $this->deliveryAddressModel = new CustomerDeliveryAddressModel();
    }

    public function index()
    {
        $keyword = $this->request->getGet('keyword');
        $page = $this->request->getGet('page') ?: 1;

        $data = $this->orderModel->getList($keyword, $page);
        $pagerData = [
            'currentPage' => $data['currentPage'],
            'totalPages' => $data['totalPages']
        ];

        return view('order/index', [
            'data' => $data['data'],
            'keyword' => $keyword,
            'pager' => $pagerData
        ]);
    }

    public function create()
    {
        return view('order/form', [
            'isEdit' => false,
            'customers' => $this->customerModel->findAll(),
            'products' => $this->productModel->findAll(),
            'productCategories' => $this->productCategoryModel->getAllForDropdown(),
            'deliveryAddressMissing' => false,
            'orderNumber' => $this->orderModel->generateOrderNumber()
        ]);
    }

    public function createFromQuote($quoteId)
    {
        $orderId = $this->orderModel->createFromQuote($quoteId);

        if ($orderId) {
            return redirect()->to(url_to('QuoteController::index'))->with('success', '報價單已成功轉換為訂單');
        }

        return redirect()->back()->with('error', '建立訂單失敗，請確認報價單尚未轉換過');
    }

    public function edit($id)
    {
        $data = $this->orderModel->getOrderWithItems($id);
        if (!$data) {
            return redirect()->to(url_to('OrderController::index'))->with('error', '訂單不存在');
        }

        $deliveryAddressMissing = false;
        if (!empty($data['o_cda_id']) && !$this->deliveryAddressModel->find($data['o_cda_id'])) {
            $deliveryAddressMissing = true;
            $data['o_cda_id'] = null;
        }

        return view('order/form', [
            'isEdit' => true,
            'data' => $data,
            'customers' => $this->customerModel->findAll(),
            'products' => $this->productModel->findAll(),
            'productCategories' => $this->productCategoryModel->getAllForDropdown(),
            'deliveryAddressMissing' => $deliveryAddressMissing,
        ]);
    }

    public function save()
    {
        $data = $this->request->getPost();
        $items = $this->request->getPost('items');

        $customerId = $data['o_c_id'] ?? null;
        $deliveryAddressId = $data['o_cda_id'] ?? null;

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

        $result = $this->orderModel->saveOrderWithItems($data, $items);

        if (!$result['success']) {
            return redirect()->back()
                ->withInput()
                ->with('error', $result['message']);
        }

        return redirect()->to(url_to('OrderController::index'));
    }

    public function delete($id)
    {
        // 檢查訂單是否存在
        $order = $this->orderModel->find($id);
        if (!$order) {
            return redirect()->to(url_to('OrderController::index'))->with('error', '訂單不存在');
        }

        // 因為設定了 CASCADE，刪除訂單時會自動刪除相關的訂單項目
        if ($this->orderModel->delete($id)) {
            return redirect()->to(url_to('OrderController::index'));
        } else {
            return redirect()->to(url_to('OrderController::index'))->with('error', '訂單刪除失敗');
        }
    }
}
