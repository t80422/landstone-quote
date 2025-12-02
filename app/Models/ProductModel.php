<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'p_id';
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
    protected $createdField = 'p_created_at';
    protected $updatedField = 'p_updated_at';

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
