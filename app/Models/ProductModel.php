<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'p_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'p_code',
        'p_barcode',
        'p_name',
        'p_image',
        'p_specifications',
        'p_standard_price',
        'p_unit',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'p_created_at';
    protected $updatedField = 'p_updated_at';

    // Validation
    protected $validationRules = [
        'p_code' => 'required|max_length[50]|is_unique[products.p_code,p_id,{p_id}]',
        'p_name' => 'required|max_length[255]',
        'p_standard_price' => 'permit_empty|integer',
        'p_unit' => 'permit_empty|max_length[20]',
    ];

    protected $validationMessages = [
        'p_code' => [
            'required' => '產品編號為必填',
            'is_unique' => '產品編號已存在',
        ],
        'p_name' => [
            'required' => '產品名稱為必填',
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
}

