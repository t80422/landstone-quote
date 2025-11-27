<?php

namespace App\Models;

use CodeIgniter\Model;

class QuoteItemModel extends Model
{
    protected $table = 'quote_items';
    protected $primaryKey = 'qi_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'qi_q_id',
        'qi_p_id',
        'qi_quantity',
        'qi_unit_price',
        'qi_discount',
        'qi_amount',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'qi_created_at';
    protected $updatedField = 'qi_updated_at';

    // Validation
    protected $validationRules = [
        'qi_q_id' => 'required|integer',
        'qi_p_id' => 'required|integer',
        'qi_quantity' => 'required|integer|greater_than[0]',
        'qi_unit_price' => 'permit_empty|integer',
        'qi_discount' => 'permit_empty|decimal',
    ];

    protected $validationMessages = [
        'qi_q_id' => [
            'required' => '報價單為必填',
        ],
        'qi_p_id' => [
            'required' => '商品為必填',
        ],
        'qi_quantity' => [
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
     * 取得報價單明細及商品資料
     */
    public function getItemsWithProduct($quoteId)
    {
        return $this->select('quote_items.*, products.p_name, products.p_code, products.p_specifications, products.p_unit')
                    ->join('products', 'products.p_id = quote_items.qi_p_id', 'left')
                    ->where('qi_q_id', $quoteId)
                    ->findAll();
    }
}

