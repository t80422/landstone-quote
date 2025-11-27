<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderItemModel extends Model
{
    protected $table = 'order_items';
    protected $primaryKey = 'oi_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'oi_o_id',
        'oi_p_id',
        'oi_quantity',
        'oi_unit_price',
        'oi_amount',
        'oi_shipped_quantity',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'oi_created_at';
    protected $updatedField = 'oi_updated_at';

    // Validation
    protected $validationRules = [
        'oi_o_id' => 'required|integer',
        'oi_p_id' => 'required|integer',
        'oi_quantity' => 'required|integer|greater_than[0]',
        'oi_unit_price' => 'permit_empty|integer',
    ];

    protected $validationMessages = [
        'oi_o_id' => [
            'required' => '訂單為必填',
        ],
        'oi_p_id' => [
            'required' => '商品為必填',
        ],
        'oi_quantity' => [
            'required' => '數量為必填',
            'greater_than' => '數量必須大於 0',
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
     * 取得訂單明細及商品資料
     */
    public function getItemsWithProduct($orderId)
    {
        return $this->select('order_items.*, products.p_name, products.p_code, products.p_specifications, products.p_unit')
                    ->join('products', 'products.p_id = order_items.oi_p_id', 'left')
                    ->where('oi_o_id', $orderId)
                    ->findAll();
    }

    /**
     * 取得待出貨數量
     */
    public function getPendingQuantity($itemId)
    {
        $item = $this->find($itemId);
        if (!$item) {
            return 0;
        }
        return $item['oi_quantity'] - $item['oi_shipped_quantity'];
    }

    /**
     * 更新已出貨數量
     */
    public function updateShippedQuantity($itemId, $quantity)
    {
        $item = $this->find($itemId);
        if (!$item) {
            return false;
        }
        
        $newShippedQuantity = $item['oi_shipped_quantity'] + $quantity;
        
        return $this->update($itemId, [
            'oi_shipped_quantity' => $newShippedQuantity
        ]);
    }
}

