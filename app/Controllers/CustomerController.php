<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CustomerModel;
use App\Models\CustomerDeliveryAddressModel;

class CustomerController extends BaseController
{
    private $customerModel;
    private $deliveryAddressModel;

    public function __construct()
    {
        $this->customerModel = new CustomerModel();
        $this->deliveryAddressModel = new CustomerDeliveryAddressModel();
    }

    public function index()
    {
        $keyword = $this->request->getGet('keyword');
        $page = $this->request->getGet('page') ?: 1;
        $data = $this->customerModel->getList($keyword, $page);
        $pagerData = [
            'currentPage' => $data['currentPage'],
            'totalPages' => $data['totalPages']
        ];

        return view('customer/index', [
            'data' => $data['data'],
            'keyword' => $keyword,
            'pager' => $pagerData
        ]);
    }

    public function create()
    {
        return view('customer/form', [
            'isEdit' => false,
        ]);
    }

    public function edit($id)
    {
        $data = $this->customerModel->getCustomerWithAddresses($id);
        return view('customer/form', [
            'isEdit' => true,
            'data' => $data,
        ]);
    }

    public function save()
    {
        $data = $this->request->getPost();
        $deliveryAddresses = $this->request->getPost('delivery_addresses');

        // 驗證至少要有一個送貨地址
        if (empty($deliveryAddresses) || count(array_filter($deliveryAddresses, function($addr) {
            return !empty($addr['cda_address']);
        })) === 0) {
            return redirect()->back()->withInput()->with('error', '至少需要新增一個送貨地址');
        }

        // 開始事務
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // 儲存客戶基本資料
            $customerId = $data['c_id'] ?? null;
            
            if ($customerId) {
                // 更新
                $this->customerModel->update($customerId, $data);
            } else {
                // 新增
                $customerId = $this->customerModel->insert($data);
            }

            // 處理送貨地址
            if ($deliveryAddresses) {
                // 取得要刪除的地址 ID
                $deletedIds = $this->request->getPost('deleted_address_ids');
                if ($deletedIds) {
                    $deletedIds = explode(',', $deletedIds);
                    foreach ($deletedIds as $deleteId) {
                        if (!empty($deleteId)) {
                            $this->deliveryAddressModel->deleteAddress($deleteId);
                        }
                    }
                }

                // 儲存或更新送貨地址
                foreach ($deliveryAddresses as $index => $address) {
                    // 過濾空地址
                    if (empty($address['cda_address'])) {
                        continue;
                    }

                    $address['cda_c_id'] = $customerId;
                    
                    // 處理預設地址
                    $address['cda_is_default'] = isset($address['cda_is_default']) ? 1 : 0;

                    if (!empty($address['cda_id'])) {
                        // 更新現有地址
                        $this->deliveryAddressModel->update($address['cda_id'], $address);
                    } else {
                        // 新增地址
                        $this->deliveryAddressModel->insert($address);
                    }
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->withInput()->with('error', '儲存失敗，請稍後再試');
            }

            return redirect()->to(url_to('CustomerController::index'));

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', '儲存失敗：' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        // 因為設定了 CASCADE，刪除客戶時會自動刪除相關的送貨地址
        $this->customerModel->delete($id);
        return redirect()->to(url_to('CustomerController::index'))->with('success', '刪除成功');
    }

    /**
     * AJAX: 刪除送貨地址
     */
    public function deleteDeliveryAddress($id)
    {
        if ($this->request->isAJAX()) {
            $result = $this->deliveryAddressModel->deleteAddress($id);
            return $this->response->setJSON([
                'success' => $result,
                'message' => $result ? '刪除成功' : '刪除失敗'
            ]);
        }
        return $this->response->setStatusCode(400);
    }
}
