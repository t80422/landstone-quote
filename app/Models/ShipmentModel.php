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
     * 獲取出貨單列表（支援搜尋、訂單篩選和分頁）
     */
    public function getList($keyword = null, $page = 1, $orderId = null)
    {
        $builder = $this->builder()
            ->select('shipments.s_id, shipments.s_number, shipments.s_date, shipments.s_status, shipments.s_notes, shipments.s_created_at, shipments.s_o_id, orders.o_number, customers.c_name')
            ->join('orders', 'orders.o_id = shipments.s_o_id')
            ->join('customers', 'customers.c_id = orders.o_c_id');

        // 訂單篩選
        if ($orderId) {
            $builder->where('shipments.s_o_id', $orderId);
        }

        // 關鍵字搜尋
        if ($keyword) {
            $builder->groupStart()
                ->like('shipments.s_number', $keyword)
                ->orLike('orders.o_number', $keyword)
                ->orLike('customers.c_name', $keyword)
                ->groupEnd();
        }

        $builder->orderBy('shipments.s_created_at', 'DESC');

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
     * 取得訂單的所有出貨單
     */
    public function getByOrder($orderId)
    {
        return $this->where('s_o_id', $orderId)->findAll();
    }

    /**
     * 儲存出貨單及其項目
     * 同時更新訂單項目的已出貨數量和訂單的出貨狀態
     */
    public function saveShipmentWithItems(array $shipmentData, array $items): array
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $shipmentItemModel = new ShipmentItemModel();
            $orderModel = new OrderModel();

            $shipmentId = $shipmentData['s_id'] ?? null;
            $orderId = $shipmentData['s_o_id'];

            // 1. 儲存出貨單
            if ($shipmentId) {
                $this->update($shipmentId, $shipmentData);

                // 編輯模式：先回復之前的出貨數量，再重新扣除
                $oldItems = $shipmentItemModel->where('si_s_id', $shipmentId)->findAll();
                foreach ($oldItems as $oldItem) {
                    $db->table('order_items')
                        ->where('oi_id', $oldItem['si_oi_id'])
                        ->increment('oi_shipped_quantity', -$oldItem['si_quantity']);
                }
                $shipmentItemModel->where('si_s_id', $shipmentId)->delete();
            } else {
                $shipmentId = $this->insert($shipmentData);
            }

            // 2. 儲存出貨明細並更新訂單項目已出貨數量
            foreach ($items as $item) {
                if (empty($item['si_quantity']) || $item['si_quantity'] <= 0) {
                    continue;
                }

                $item['si_s_id'] = $shipmentId;
                $shipmentItemModel->insert($item);

                // 更新訂單項目的已出貨數量
                $db->table('order_items')
                    ->where('oi_id', $item['si_oi_id'])
                    ->increment('oi_shipped_quantity', $item['si_quantity']);
            }

            // 3. 更新訂單出貨狀態
            $orderModel->updateShipmentStatus($orderId);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return [
                    'success' => false,
                    'message' => '儲存失敗，請稍後再試',
                ];
            }

            return [
                'success' => true,
                'message' => '儲存成功',
                'shipmentId' => $shipmentId,
            ];
        } catch (\Exception $e) {
            $db->transRollback();
            return [
                'success' => false,
                'message' => '儲存失敗：' . $e->getMessage(),
            ];
        }
    }

    /**
     * 生成新的出貨單號
     * 格式：S + 年月日 + 流水號(3位)
     * 例如：S20251128001
     * 
     * @return string
     */
    public function generateShipmentNumber(): string
    {
        $date = date('Ymd');
        $prefix = 'S' . $date;

        $lastShipment = $this->like('s_number', $prefix, 'after')
            ->orderBy('s_number', 'DESC')
            ->first();

        if ($lastShipment) {
            $lastNumber = intval(substr($lastShipment['s_number'], -3));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * 刪除出貨單
     * 同時回復訂單項目的已出貨數量和訂單的出貨狀態
     */
    public function deleteShipment($id)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $shipment = $this->find($id);
            if (!$shipment) {
                throw new \Exception('出貨單不存在');
            }

            $shipmentItemModel = new ShipmentItemModel();
            $orderModel = new OrderModel();

            // 1. 回復已出貨數量
            $items = $shipmentItemModel->where('si_s_id', $id)->findAll();
            foreach ($items as $item) {
                $db->table('order_items')
                    ->where('oi_id', $item['si_oi_id'])
                    ->increment('oi_shipped_quantity', -$item['si_quantity']);
            }

            // 2. 刪除出貨單
            $this->delete($id);

            // 3. 更新訂單出貨狀態
            $orderModel->updateShipmentStatus($shipment['s_o_id']);

            $db->transComplete();
            return true;
        } catch (\Exception $e) {
            $db->transRollback();
            return false;
        }
    }
}
