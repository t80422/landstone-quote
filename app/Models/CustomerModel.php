<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomerModel extends Model
{
    protected $table = 'customers';
    protected $primaryKey = 'c_id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'c_name',
        'c_contact_person',
        'c_manager',
        'c_phone',
        'c_fax',
        'c_email',
        'c_address',
        'c_tax_id',
        'c_payment_method',
        'c_notes',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'c_created_at';
    protected $updatedField = 'c_updated_at';

    // Callbacks
    protected $allowCallbacks = true;

    public function getList($keyword = null, $page = 1)
    {
        $builder = $this->builder()
            ->select('c_id, c_name, c_contact_person, c_phone, c_created_at, c_updated_at');

        if ($keyword) {
            $builder->like('c_name', $keyword)
                ->orLike('c_contact_person', $keyword);
        }

        $builder->orderBy('c_created_at', 'DESC');

        $total = $builder->countAllResults(false);
        $perPage = 10;
        $totalPages = ceil($total / $perPage);
        $data = $builder->limit($perPage, ($page - 1) * $perPage)->get()->getResultArray();

        return [
            'data' => $data,
            'currentPage' => $page,
            'totalPages' => $totalPages,
        ];
    }

    /**
     * 取得客戶及其送貨地址
     */
    public function getCustomerWithAddresses($customerId)
    {
        $customer = $this->find($customerId);
        
        if ($customer) {
            $addressModel = new \App\Models\CustomerDeliveryAddressModel();
            $customer['delivery_addresses'] = $addressModel->getByCustomerId($customerId);
        }
        
        return $customer;
    }

    /**
     * 取得客戶的預設送貨地址
     */
    public function getDefaultDeliveryAddress($customerId)
    {
        $addressModel = new \App\Models\CustomerDeliveryAddressModel();
        return $addressModel->getDefaultAddress($customerId);
    }

    /**
     * 驗證客戶至少有一個送貨地址
     */
    public function validateHasDeliveryAddress($customerId)
    {
        $addressModel = new \App\Models\CustomerDeliveryAddressModel();
        $addresses = $addressModel->getByCustomerId($customerId);
        return count($addresses) > 0;
    }
}
