<?php

namespace App\Models;

use CodeIgniter\Model;

class ShipmentModel extends Model
{
    protected $table = 'shipments';
    protected $primaryKey = 's_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        's_o_id',
        's_number',
        's_date',
        's_status',
        's_notes',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 's_created_at';
    protected $updatedField = 's_updated_at';

    // Validation
    protected $validationRules = [
        's_o_id' => 'required|integer',
        's_number' => 'required|max_length[50]',
        's_date' => 'required|valid_date',
        's_status' => 'permit_empty|in_list[preparing,partial,completed]',
    ];

    protected $validationMessages = [
        's_o_id' => [
            'required' => '訂單為必填',
        ],
        's_number' => [
            'required' => '出貨單號為必填',
        ],
        's_date' => [
            'required' => '出貨日期為必填',
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
     * 取得出貨單及訂單資料
     */
    public function getWithOrder($id = null)
    {
        $builder = $this->select('shipments.*, orders.o_number as order_number, orders.o_date')
                        ->join('orders', 'orders.o_id = shipments.s_o_id', 'left');
        
        if ($id !== null) {
            return $builder->where('shipments.s_id', $id)->first();
        }
        
        return $builder->findAll();
    }

    /**
     * 取得訂單的所有出貨單
     */
    public function getByOrder($orderId)
    {
        return $this->where('s_o_id', $orderId)->findAll();
    }
}

