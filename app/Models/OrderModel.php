<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderModel extends Model
{
    protected $table = 'orders';
    protected $primaryKey = 'o_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'o_number',
        'o_date',
        'o_c_id',
        'o_q_id',
        'o_delivery_date',
        'o_total_amount',
        'o_payment_status',
        'o_invoice_number',
        'o_status',
        'o_notes',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'o_created_at';
    protected $updatedField = 'o_updated_at';

    // Validation
    protected $validationRules = [
        'o_number' => 'required|max_length[50]|is_unique[orders.o_number,o_id,{o_id}]',
        'o_date' => 'required|valid_date',
        'o_c_id' => 'required|integer',
        'o_payment_status' => 'permit_empty|in_list[unpaid,partial,paid]',
        'o_status' => 'permit_empty|in_list[processing,completed,cancelled]',
    ];

    protected $validationMessages = [
        'o_number' => [
            'required' => '訂單編號為必填',
            'is_unique' => '訂單編號已存在',
        ],
        'o_date' => [
            'required' => '訂單日期為必填',
        ],
        'o_c_id' => [
            'required' => '客戶為必填',
        ],
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    /**
     * 取得訂單及客戶資料
     */
    public function getWithCustomer($id = null)
    {
        $builder = $this->select('orders.*, customers.c_name as customer_name, customers.c_contact_person, customers.c_phone, customers.c_address')
                        ->join('customers', 'customers.c_id = orders.o_c_id', 'left');
        
        if ($id !== null) {
            return $builder->where('orders.o_id', $id)->first();
        }
        
        return $builder->findAll();
    }

    /**
     * 取得訂單及來源報價單
     */
    public function getWithQuote($id)
    {
        return $this->select('orders.*, quotes.q_number as quote_number')
                    ->join('quotes', 'quotes.q_id = orders.o_q_id', 'left')
                    ->where('orders.o_id', $id)
                    ->first();
    }
}

